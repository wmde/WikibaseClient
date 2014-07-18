<?php

namespace Wikibase\Client\Hooks;

use Parser;
use Wikibase\PropertyParserFunctionRunner;

class ParserFunctionRegistrant {

	/**
	 * @param boolean - setting to enable use of property parser function
	 */
	private $allowDataTransclusion;

	/**
	 * @param boolean $allowDataTransclusion
	 */
	public function __construct( $allowDataTransclusion ) {
		$this->allowDataTransclusion = $allowDataTransclusion;
	}

	/**
	 * @param Parser $parser
	 */
	public function register( Parser $parser ) {
		$this->registerNoLangLinkHandler( $parser );
		$this->registerPropertyParserFunction( $parser );
	}

	private function registerNoLangLinkHandler( Parser $parser ) {
		$parser->setFunctionHook(
			'noexternallanglinks',
			'\Wikibase\NoLangLinkHandler::handle',
			SFH_NO_HASH
		);
	}

	private function registerPropertyParserFunction( Parser $parser ) {
		if( !$this->allowDataTransclusion ) {
			return;
		}

		$parser->setFunctionHook(
			'property',
			function( Parser $parser, $propertyLabel ) {
				return PropertyParserFunctionRunner::render( $parser, $propertyLabel );
			}
		);

	}
}
