<?php

namespace Wikibase;

use ValueFormatters\FormatterOptions;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\SimpleSiteLink;
use Wikibase\Lib\SnakFormatter;

/**
 * Handler of the {{#property}} parser function.
 *
 * TODO: cleanup injection of dependencies
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 0.4
 *
 * @file
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 */
class PropertyParserFunction {

	/* @var \Language */
	protected $language;

	/* @var EntityLookup */
	protected $entityLookup;

	/* @var PropertyLabelResolver */
	protected $propertyLabelResolver;

	/* @var ParserErrorMessageFormatter */
	protected $errorFormatter;

	/* @var SnakFormatter */
	protected $snaksFormatter;

	/**
	 * @since    0.4
	 *
	 * @param \Language                   $language
	 * @param EntityLookup                $entityLookup
	 * @param PropertyLabelResolver       $propertyLabelResolver
	 * @param ParserErrorMessageFormatter $errorFormatter
	 * @param SnakFormatter               $snaksFormatter
	 */
	public function __construct( \Language $language,
		EntityLookup $entityLookup, PropertyLabelResolver $propertyLabelResolver,
		ParserErrorMessageFormatter $errorFormatter, SnakFormatter $snaksFormatter ) {
		$this->language = $language;
		$this->entityLookup = $entityLookup;
		$this->propertyLabelResolver = $propertyLabelResolver;
		$this->errorFormatter = $errorFormatter;
		$this->snaksFormatter = $snaksFormatter;
	}

	/**
	 * Returns such Claims from $entity that have a main Snak for the property that
	 * is specified by $propertyLabel.
	 *
	 * @param Entity $entity The Entity from which to get the clams
	 * @param string $propertyLabel A property label (in the wiki's content language) or a prefixed property ID.
	 *
	 * @return Claims The claims for the given property.
	 */
	private function getClaimsForProperty( Entity $entity, $propertyLabel ) {
		$propertyIdToFind = EntityId::newFromPrefixedId( $propertyLabel );

		if ( $propertyIdToFind === null ) {
			//XXX: It might become useful to give the PropertyLabelResolver a hint as to which
			//     properties may become relevant during the present request, namely the ones
			//     used by the Item linked to the current page. This could be done with
			//     something like this:
			//
			//     $this->propertyLabelResolver->preloadLabelsFor( $propertiesUsedByItem );

			$propertyIds = $this->propertyLabelResolver->getPropertyIdsForLabels( array( $propertyLabel ) );

			if ( empty( $propertyIds ) ) {
				return new Claims();
			} else {
				$propertyIdToFind = $propertyIds[$propertyLabel];
			}
		}

		$allClaims = new Claims( $entity->getClaims() );
		$claims = $allClaims->getClaimsForProperty( $propertyIdToFind->getNumericId() );

		return $claims;
	}

	/**
	 * @since 0.4
	 *
	 * @param Snak[] $snaks
	 *
	 * @return string - wikitext format
	 */
	private function formatSnakList( $snaks ) {
		$formattedValues = $this->formatSnaks( $snaks );
		return $this->language->commaList( $formattedValues );
	}

	private function formatSnaks( $snaks ) {
		$strings = array();

		foreach ( $snaks as $snak ) {
			$strings[] = $this->snaksFormatter->formatSnak( $snak );
		}

		return $strings;
	}

	/**
	 * @since 0.4
	 *
	 * @param EntityId $entityId
	 * @param string   $propertyLabel
	 *
	 * @return \Status a status object wrapping a wikitext string
	 */
	public function renderForEntityId( EntityId $entityId, $propertyLabel ) {
		wfProfileIn( __METHOD__ );

		try {
			$entity = $this->entityLookup->getEntity( $entityId );

			if ( !$entity ) {
				wfProfileOut( __METHOD__ );
				return \Status::newGood( '' );
			}

			$claims = $this->getClaimsForProperty( $entity, $propertyLabel );

			if ( $claims->isEmpty() ) {
				wfProfileOut( __METHOD__ );
				return \Status::newGood( '' );
			}

			$snakList = $claims->getMainSnaks();
			$text = $this->formatSnakList( $snakList, $propertyLabel );
			$status = \Status::newGood( $text );
		} catch ( \Exception $ex ) {
			wfDebugLog( __CLASS__, __FUNCTION__ . ': ' . $ex->getMessage() );

			$status = \Status::newFatal( 'wikibase-property-render-error', $propertyLabel, $ex->getMessage() );
		}

		wfProfileOut( __METHOD__ );
		return $status;
	}

	/**
	 * @since 0.4
	 *
	 * @param \Parser &$parser
	 * @param string $propertyLabel property label or ID (pXXX)
	 *
	 * @return array
	 */
	public static function render( \Parser $parser, $propertyLabel ) {
		wfProfileIn( __METHOD__ );
		$siteId = Settings::get( 'siteGlobalID' );

		$siteLinkLookup = WikibaseClient::getDefaultInstance()->getStore()->getSiteLinkTable();
		$entityId = $siteLinkLookup->getEntityIdForSiteLink( //FIXME: method not in the interface
			new SimpleSiteLink( $siteId, $parser->getTitle()->getFullText() )
		);

		// @todo handle when site link is not there, such as site link / entity has been deleted...
		if ( $entityId === null ) {
			wfProfileOut( __METHOD__ );
			return '';
		}

		$targetLanguage = $parser->getTargetLanguage();
		$errorFormatter = new ParserErrorMessageFormatter( $targetLanguage );

		$wikibaseClient = WikibaseClient::getDefaultInstance();

		$entityLookup = $wikibaseClient->getStore()->getEntityLookup();
		$propertyLabelResolver = $wikibaseClient->getStore()->getPropertyLabelResolver();

		$languageFallbackChainFactory = WikibaseClient::getDefaultInstance()->getLanguageFallbackChainFactory();
		$languageFallbackChain = $languageFallbackChainFactory->newFromLanguage( $targetLanguage,
			LanguageFallbackChainFactory::FALLBACK_SELF | LanguageFallbackChainFactory::FALLBACK_VARIANTS
		);

		$options = new FormatterOptions( array(
			'languages' => $languageFallbackChain,
			// ...more options...
		) );

		$formatter = $wikibaseClient->newSnakFormatter( SnakFormatter::FORMAT_WIKI, $options );

		$instance = new self( $targetLanguage,
			$entityLookup, $propertyLabelResolver,
			$errorFormatter, $formatter );

		$status = $instance->renderForEntityId( $entityId, $propertyLabel );

		if ( !$status->isGood() ) {
			// stuff the error messages into the ParserOutput, so we can render them later somewhere

			$errors = $parser->getOutput()->getExtensionData( 'wikibase-property-render-errors' );
			if ( $errors === null ) {
				$errors = array();
			}

			//XXX: if Status sucked less, we'd could get an array of Message objects
			$errors[] = $status->getWikiText();

			$parser->getOutput()->setExtensionData( 'wikibase-property-render-errors', $errors );
		}

		$text = $status->isOK() ? $status->getValue() : '';

		$result = array(
			$text,
			'noparse' => false, // parse wikitext
			'nowiki' => false,  // formatters take care of escaping as needed
		);

		wfProfileOut( __METHOD__ );
		return $result;
	}

}
