<?php

namespace Wikibase\Client\Scribunto\Test;

use TestSites;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Snak\Snak;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\SnakFactory;
use DataValues\DataValue;
use Wikibase\DataModel\Entity\Property;
use DataValues\StringValue;
use Wikibase\DataModel\SiteLink;
use Wikibase\Lib\V4GuidGenerator;
use Wikibase\Test\MockClientStore;
use Wikibase\Client\WikibaseClient;

/**
 * Helper class for Lua integration tests.
 *
 * @since 0.5
 *
 * @license GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */
class WikibaseLuaIntegrationTestItemSetUpHelper {

	/* @var MockRepository */
	protected $mockRepository;

	public function __construct() {
		$clientStore = new MockClientStore();
		$this->mockRepository = $clientStore->getEntityLookup();
	}

	/**
	 * Sets up the test data.
	 */
	public function setUp() {
		$siteLink = new SiteLink(
			WikibaseClient::getDefaultInstance()->getSettings()->get( 'siteGlobalID' ),
			'WikibaseClientLuaTest'
		);

		if ( $this->mockRepository->getEntityIdForSiteLink( $siteLink ) ) {
			// Already set up for this MockRepository
			return;
		}

		TestSites::insertIntoDb();
		$property = $this->createTestProperty();

		$snak = $this->getTestSnak(
			$property->getId(),
			new StringValue( 'Lua :)' )
		);

		$testClaim = $this->getTestClaim( $snak );

		$siteLinks = array( $siteLink );
		$siteLinks[] = new SiteLink(
			'fooSiteId',
			'FooBarFoo'
		);

		$labels = array(
			'de' => 'Lua Test Item',
			'en' => 'Test all the code paths'
		);

		$this->createTestItem( $labels, array( $testClaim ), $siteLinks );
	}

	/**
	 * @return Property
	 */
	protected function createTestProperty() {
		$property = Property::newEmpty();
		$property->setDataTypeId( 'wikibase-item' );
		$property->setLabel( 'de', 'LuaTestProperty' );

		$this->mockRepository->putEntity( $property );

		return $property;
	}

	/**
	 * @param array $label
	 * @param Claim[]|null $claims
	 * @param array $siteLinks
	 *
	 * @return Item
	 */
	protected function createTestItem( array $labels, array $claims = null, array $siteLinks = null ) {
		$item = Item::newEmpty();
		$item->setLabels( $labels );

		if ( is_array( $siteLinks ) ) {
			foreach( $siteLinks as $siteLink ) {
				$item->addSiteLink( $siteLink );
			}
		}

		if ( is_array( $claims ) ) {
			foreach( $claims as $claim ) {
				$item->addClaim( $claim );
			}
		}

		$this->mockRepository->putEntity( $item );

		return $item;
	}

	/**
	 * @param PropertyId $propertyId
	 * @param DataValue $value
	 * @return Snak
	 */
	protected function getTestSnak( PropertyId $propertyId, DataValue $value ) {
		$snakFactory = new SnakFactory();
		$snak = $snakFactory->newSnak( $propertyId, 'value', $value );

		return $snak;
	}

	/**
	 * @param Snak $mainSnak
	 * @return Claim
	 */
	protected function getTestClaim( Snak $mainSnak ) {
		$claim = new Claim( $mainSnak );
		$guidGen = new V4GuidGenerator();
		$claim->setGuid( $guidGen->newGuid() );

		return $claim;
	}
}