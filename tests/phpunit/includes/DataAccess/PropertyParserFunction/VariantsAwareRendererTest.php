<?php

namespace Wikibase\Test;

use Language;
use Wikibase\DataAccess\PropertyParserFunction\VariantsAwareRenderer;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @covers Wikibase\DataAccess\PropertyParserFunction\VariantsAwareRenderer
 *
 * @group Wikibase
 * @group WikibaseClient
 * @group PropertyParserFunctionTest
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class VariantsAwareRendererTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider renderProvider
	 */
	public function testRender( $expected, $itemId, $variants, $propertyLabel ) {
		$languageRenderer = $this->getLanguageAwareRenderer();

		$rendererFactory = $this->getMockBuilder(
				'Wikibase\DataAccess\PropertyParserFunction\RendererFactory'
			)
			->disableOriginalConstructor()
			->getMock();

		$rendererFactory->expects( $this->any() )
			->method( 'newLanguageAwareRenderer' )
			->will( $this->returnValue( $languageRenderer ) );

		$variantsRenderer = new VariantsAwareRenderer(
			$rendererFactory,
			$variants
		);

		$result = $variantsRenderer->render( $itemId, $propertyLabel );

		$this->assertEquals( $expected, $result );
	}

	public function renderProvider() {
		$itemId = new ItemId( 'Q3' );

		return array(
			array(
				'-{zh:mooooo;zh-hans:mooooo;zh-hant:mooooo;zh-cn:mooooo;zh-hk:mooooo;}-',
				$itemId,
				array ( 'zh', 'zh-hans', 'zh-hant', 'zh-cn', 'zh-hk' ),
				'cat'
			),
			// Don't create "-{}-" for empty input,
			// to keep the ability to check a missing property with {{#if: }}.
			array(
				'',
				$itemId,
				array(),
				'cat'
			)
		);
	}

	private function getLanguageAwareRenderer() {
		$languageRenderer = $this->getMockBuilder(
			'Wikibase\DataAccess\PropertyParserFunction\Renderer'
		)
		->disableOriginalConstructor()
		->getMock();

		$languageRenderer->expects( $this->any() )
			->method( 'render' )
			->will( $this->returnValue( 'mooooo' ) );

		return $languageRenderer;
	}

}
