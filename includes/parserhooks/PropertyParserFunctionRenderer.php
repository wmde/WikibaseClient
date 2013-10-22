<?php

namespace Wikibase;

use Wikibase\Client\WikibaseClient;
use Wikibase\Lib\SnakFormatter;

/**
 * Renderer of the {{#property}} parser function.
 *
 * @since 0.5
 *
 * @file
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 * @author Liangent < liangent@gmail.com >
 */
class PropertyParserFunctionRenderer {

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
	 * @param \Language                   $language
	 * @param EntityLookup                $entityLookup
	 * @param PropertyLabelResolver       $propertyLabelResolver
	 * @param ParserErrorMessageFormatter $errorFormatter
	 * @param Lib\SnakFormatter           $snaksFormatter
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
		$claims = $allClaims->getClaimsForProperty( $propertyIdToFind );

		return $claims;
	}

	/**
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

}
