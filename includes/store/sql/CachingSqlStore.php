<?php

namespace Wikibase;

/**
 * Implementation of the client store interface using an SQL backend via MediaWiki's
 * storage abstraction layer.
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
 * @since 0.3
 *
 * @file
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 *
 * @todo: rename to MirrorSqlStore
 */
class CachingSqlStore implements ClientStore {

	/**
	 * @var EntityLookup
	 */
	private $entityLookup = null;

	/**
	 * @see Store::newSiteLinkTable
	 *
	 * @since 0.3
	 *
	 * @return SiteLinkLookup
	 */
	public function newSiteLinkTable() {
		return new SiteLinkTable( 'wbc_items_per_site', true );
	}

	/**
	 * Returns a new EntityCache instance
	 *
	 * @since 0.3
	 *
	 * @return EntityCache
	 *
	 * @todo: rename to newEntityMirror
	 */
	public function newEntityCache() {
		return new EntityCacheTable();
	}

	/**
	 * @see Store::getEntityLookup
	 *
	 * @since 0.4
	 *
	 * @return EntityLookup
	 */
	public function getEntityLookup() {
		if ( !$this->entityLookup ) {
			$this->entityLookup = $this->newEntityLookup();
		}

		return $this->entityLookup;
	}

	/**
	 * Create a new EntityLookup
	 *
	 * @return CachingEntityLoader
	 */
	protected function newEntityLookup() {
		$mirror = $this->newEntityCache();
		return new CachingEntityLoader( $mirror );
	}

	/**
	 * Throws an MWException, because no changes table is available.
	 *
	 * @since 0.4
	 *
	 * @throws \MWException because no changes table can be supplied.
	 */
	public function newChangesTable() {
		throw new \MWException( "no changes table available" );
	}

	/**
	 * Delete client store data
	 *
	 * @since 0.2
	 */
	public function clear() {
		$this->newEntityCache()->clear();

		$tables = array(
			'wbc_item_usage',
			'wbc_query_usage',
		);

		$dbw = wfGetDB( DB_MASTER );

		foreach ( $tables as $table ) {
			$dbw->delete( $dbw->tableName( $table ), '*', __METHOD__ );
		}
	}

	/**
	 * Rebuild client store data
	 *
	 * @since 0.2
	 */
	public function rebuild() {
		$this->clear();
	}

}
