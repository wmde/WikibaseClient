<?php

namespace Wikibase\Test;

use DataValues\StringValue;
use Wikibase\Claim;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\Item;
use Wikibase\ParserErrorMessageFormatter;
use Wikibase\Property;
use Wikibase\PropertyParserFunctionRenderer;
use Wikibase\PropertyValueSnak;

/**
 * @covers Wikibase\PropertyParserFunctionRenderer
 *
 * @file
 * @since 0.4
 *
 * @ingroup WikibaseClient
 * @ingroup Test
 *
 * @group Wikibase
 * @group WikibaseClient
 * @group PropertyParserFunctionTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PropertyParserFunctionRendererTest extends \PHPUnit_Framework_TestCase {

	private function getDefaultInstance() {
		$wikibaseClient = WikibaseClient::getDefaultInstance();

		$targetLanguage = \Language::factory( 'en' );
		$errorFormatter = new ParserErrorMessageFormatter( $targetLanguage );
		$dataTypeFactory = $wikibaseClient->getDataTypeFactory();
		$mockRepo = $this->newMockRepository();
		$mockResolver = new MockPropertyLabelResolver( $targetLanguage->getCode(), $mockRepo );

		$formatter = $this->getMock( 'Wikibase\Lib\SnakFormatter' );
		$formatter->expects( $this->any() )
			->method( 'formatSnak' )
			->will( $this->returnValue( '(a kitten)' ) );

		return new PropertyParserFunctionRenderer(
			$targetLanguage,
			$mockRepo,
			$mockResolver,
			$errorFormatter,
			$formatter
		);
	}

	private function newMockRepository() {
		$propertyId = new PropertyId( 'P1337' );

		$entityLookup = new MockRepository();

		$item = Item::newEmpty();
		$item->setId( 42 );
		$item->addClaim( new Claim( new PropertyValueSnak(
			$propertyId,
			new StringValue( 'Please write tests before merging your code' )
		) ) );
		$item->addClaim( new Claim( new PropertyValueSnak(
			$propertyId,
			new StringValue( 'or kittens will die' )
		) ) );

		$property = Property::newEmpty();
		$property->setId( $propertyId );

		$property->setDataTypeId( 'string' );
		$property->setLabel( 'en', 'kitten' );

		$entityLookup->putEntity( $item );
		$entityLookup->putEntity( $property );

		return $entityLookup;
	}

	public static function provideRenderForEntityId() {
		return array(
			array(
				'p1337',
				'(a kitten), (a kitten)',
				'Congratulations, you just killed a kitten'
			),
			array(
				'kitten',
				'(a kitten), (a kitten)',
				'Congratulations, you just killed a kitten'
			),
		);
	}

	/**
	 * @dataProvider provideRenderForEntityId
	 */
	public function testRenderForEntityId( $name, $expected, $info ) {
		$parserFunction = $this->getDefaultInstance();

		$status = $parserFunction->renderForEntityId(
			new ItemId( 'Q42' ),
			$name
		);

		$this->assertTrue( $status->isOK() );

		$text = $status->getValue();
		$this->assertInternalType( 'string', $text );

		$this->assertEquals(
			$expected,
			$text,
			$info
		);
	}

}
