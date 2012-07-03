<?php

namespace Wikibase;
use ORMRow;

/**
 * Class representing a single local item (ie a row in the wbc_local_items).
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LocalItem extends ORMRow {

	/**
	 * @since 0.1
	 *
	 * @return Item
	 */
	public function getItem() {
		return $this->getField( 'item_data' );
	}

	/**
	 * Creates and returns a LocalItem object.
	 * When $loadFromDB is true, the local items table is queried for an existing object,
	 * which will then be returned if found. If it's false or not found, a new object
	 * is constructed.
	 *
	 * @since 0.1
	 *
	 * @param Item $item
	 * @param bool $loadFromDB
	 *
	 * @return LocalItem
	 */
	public static function newFromItem( Item $item, $loadFromDB = true ) {
		$table = \Wikibase\LocalItemsTable::singleton();
		$localItem = false;

		if ( $loadFromDB ) {
			$localItem = $table->selectRow( null, array( 'item_id' => $item->getId() ) );
		}

		if ( $localItem === false ) {
			$localItem = new static(
				$table,
				array( 'item_data' => $item )
			);
		}

		$siteLinks = $item->getSiteLinks();

		// TODO: obtain local wiki global id
		// TODO: properly manage this field
		if ( array_key_exists( 'enwiki', $siteLinks ) ) {
			$localItem->setField( 'page_title', $siteLinks['enwiki'] );
		}

		return $localItem;
	}

}