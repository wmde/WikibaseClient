<?php

namespace Wikibase;

use ValueFormatters\FormatterOptions;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\SimpleSiteLink;
use Wikibase\Lib\SnakFormatter;

/**
 * Handler of the {{#property}} parser function.
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
 * @author Liangent < liangent@gmail.com >
 */
class PropertyParserFunction {

	/**
	 * @var \Parser
	 */
	protected $parser;

	/**
	 * @var EntityId
	 */
	protected $entityId;

	/**
	 * @var EntityLookup
	 */
	protected $entityLookup;

	/**
	 * @var PropertyLabelResolver
	 */
	protected $propertyLabelResolver;

	/**
	 * Constructor.
	 *
	 * @param \Parser $parser
	 * @param EntityId $entityId
	 * @param EntityLookup                $entityLookup
	 * @param PropertyLabelResolver       $propertyLabelResolver
	 */
	public function __construct( \Parser $parser, EntityId $entityId,
		EntityLookup $entityLookup, PropertyLabelResolver $propertyLabelResolver
	) {
		$this->parser = $parser;
		$this->entityId = $entityId;
		$this->entityLookup = $entityLookup;
		$this->propertyLabelResolver = $propertyLabelResolver;
	}

	/**
	 * Check whether variants are used in this parser run.
	 *
	 * @param \Parser $parser
	 * @return bool
	 */
	public function isParserUsingVariants() {
		$parserOptions = $this->parser->getOptions();
		return $this->parser->OutputType() === \Parser::OT_HTML && !$parserOptions->getInterfaceMessage()
			&& !$parserOptions->getDisableContentConversion();
	}

	/**
	 * Post-process rendered array (variant text) into wikitext to be used in pages.
	 *
	 * @param array $textArray
	 * @return string
	 */
	public function processRenderedArray( $textArray ) {
		// We got arrays, so they must have already checked that variants are being used.
		$text = '-{';
		foreach ( $textArray as $variantCode => $variantText ) {
			$text .= "$variantCode:$variantText;";
		}
		$text .= '}-';

		return $text;
	}

	/**
	 * Build a PropertyParserFunctionRenderer object for a given language.
	 *
	 * @param \Language $language
	 * @return PropertyParserFunctionRenderer
	 */
	public function getRenderer( \Language $language ) {
		wfProfileIn( __METHOD__ );

		$wikibaseClient = WikibaseClient::getDefaultInstance();
		$errorFormatter = new ParserErrorMessageFormatter( $language );

		$languageFallbackChainFactory = $wikibaseClient->getLanguageFallbackChainFactory();
		$languageFallbackChain = $languageFallbackChainFactory->newFromLanguage( $language,
			LanguageFallbackChainFactory::FALLBACK_SELF | LanguageFallbackChainFactory::FALLBACK_VARIANTS
		);

		$options = new FormatterOptions( array(
			'languages' => $languageFallbackChain,
			// ...more options...
		) );

		$snaksFormatter = $wikibaseClient->newSnakFormatter( SnakFormatter::FORMAT_WIKI, $options );

		$instance = new PropertyParserFunctionRenderer( $language,
			$this->entityLookup, $this->propertyLabelResolver,
			$errorFormatter, $snaksFormatter );

		wfProfileIn( __METHOD__ );
		return $instance;
	}

	/**
	 * @param string $propertyLabel property label or ID (pXXX)
	 * @param \Language $language
	 *
	 * @return string
	 */
	public function renderInLanguage( $propertyLabel, \Language $language ) {

		$renderer = $this->getRenderer( $language );

		$status = $renderer->renderForEntityId( $this->entityId, $propertyLabel );

		if ( !$status->isGood() ) {
			// stuff the error messages into the ParserOutput, so we can render them later somewhere

			$errors = $this->parser->getOutput()->getExtensionData( 'wikibase-property-render-errors' );
			if ( $errors === null ) {
				$errors = array();
			}

			//XXX: if Status sucked less, we'd could get an array of Message objects
			$errors[] = $status->getWikiText();

			$this->parser->getOutput()->setExtensionData( 'wikibase-property-render-errors', $errors );
		}

		return $status->isOK() ? $status->getValue() : '';
	}

	/**
	 * @param string $propertyLabel property label or ID (pXXX)
	 * @param string[] $variants Variant codes
	 *
	 * @return string[], key by variant codes
	 */
	public function renderInVariants( $propertyLabel, array $variants ) {
		$textArray = array();

		foreach ( $variants as $variantCode ) {
			$variantLanguage = \Language::factory( $variantCode );
			$textArray[$variantCode] = $this->renderInLanguage( $propertyLabel, $variantLanguage );
		}

		return $textArray;
	}

	/**
	 * @param string $propertyLabel property label or ID (pXXX)
	 *
	 * @return string Wikitext
	 */
	public function doRender( $propertyLabel ) {
		wfProfileIn( __METHOD__ );

		$targetLanguage = $this->parser->getTargetLanguage();

		if ( $this->isParserUsingVariants() && $this->parser->getConverterLanguage()->hasVariants() ) {
			$text = $this->processRenderedArray( $this->renderInVariants(
				$propertyLabel, $this->parser->getConverterLanguage()->getVariants()
			) );
		} else {
			$text = $this->renderInLanguage( $propertyLabel, $targetLanguage );
		}

		wfProfileOut( __METHOD__ );
		return $text;
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

		$wikibaseClient = WikibaseClient::getDefaultInstance();

		$entityLookup = $wikibaseClient->getStore()->getEntityLookup();
		$propertyLabelResolver = $wikibaseClient->getStore()->getPropertyLabelResolver();

		$instance = new self( $parser, $entityId, $entityLookup, $propertyLabelResolver );

		$result = array(
			$instance->doRender( $propertyLabel ),
			'noparse' => false, // parse wikitext
			'nowiki' => false,  // formatters take care of escaping as needed
		);

		wfProfileOut( __METHOD__ );
		return $result;
	}

}
