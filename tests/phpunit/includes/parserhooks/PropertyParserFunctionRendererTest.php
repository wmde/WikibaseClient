<?php

namespace Wikibase\Test;

use DataValues\StringValue;
use Language;
use Wikibase\Claim;
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
 * @since 0.4
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
		$targetLanguage = Language::factory( 'en' );
		$errorFormatter = new ParserErrorMessageFormatter( $targetLanguage );
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

		$claim1 = new Claim( new PropertyValueSnak(
			$propertyId,
			new StringValue( 'Please write tests before merging your code' )
		) );
		$claim1->setGuid( __METHOD__ . '$' . 1 );

		$claim2 = new Claim( new PropertyValueSnak(
			$propertyId,
			new StringValue( 'or kittens will die' )
		) );
		$claim2->setGuid( __METHOD__ . '$' . 2 );

		$item = Item::newEmpty();
		$item->setId( 42 );
		$item->addClaim( $claim1 );
		$item->addClaim( $claim2 );

		$property = Property::newEmpty();
		$property->setId( $propertyId );

		$property->setDataTypeId( 'string' );
		$property->setLabel( 'en', 'kitten' );

		$entityLookup->putEntity( $item );
		$entityLookup->putEntity( $property );

		return $entityLookup;
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

	public function provideRenderForEntityId() {
		return array(
			array(
				'p1337',
				'(a kitten), (a kitten)',
				'Congratulations, you just killed a kitten'
			),
			array(
				'P1337',
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
	 * @dataProvider invalidRenderForEntityIdProvider
	 * @expectedException Wikibase\Lib\PropertyLabelNotResolvedException
	 */
	public function testInvalidRenderForEntityId( $name, $message ) {
		$parserFunction = $this->getDefaultInstance();

		$status = $parserFunction->renderForEntityId(
			new ItemId( 'Q42' ),
			$name
		);

		$this->assertFalse( $status->isOK(), $message );
	}

	public function invalidRenderForEntityIdProvider() {
		return array(
			array( 'Kitten', 'invalid label, property by label lookup is case-sensitive' )
		);
	}

}
