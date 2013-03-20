<?php

namespace Wikibase;

/**
 * Implementation of the client store interface using direct access to the repository's
 * database via MediaWiki's foreign wiki mechanism as implemented by LBFactory_multi.
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
 * @author Daniel Kinzler
 *
 * @todo: share code with CachingSqlStore
 * */
class DirectSqlStore implements ClientStore {

	/**
	 * @var EntityLookup
	 */
	private $entityLookup = null;

	/**
	 * @var PropertyLookup
	 */
	private $propertyLookup = null;

	/**
	 * @var String|bool $repoWiki
	 */
	protected $repoWiki;

	/**
	 * @param $repoWiki
	 */
	public function __construct( $repoWiki ) {
		$this->repoWiki = $repoWiki;
	}

	/**
	 * @see Store::newSiteLinkTable
	 *
	 * @since 0.3
	 *
	 * @return SiteLinkLookup
	 */
	public function newSiteLinkTable() {
		return new SiteLinkTable( 'wb_items_per_site', true, $this->repoWiki );
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
		//TODO: get config for persistent cache from config
		$lookup = new WikiPageEntityLookup( $this->repoWiki ); // entities are stored in wiki pages
		return new CachingEntityLoader( $lookup );
	}

	/**
	 * Get a PropertyLookup object
	 *
	 * @return PropertyLookup
	 */
	public function getPropertyLookup() {
		if ( !$this->propertyLookup ) {
			$this->propertyLookup = $this->newPropertyLookup();
		}

		return $this->propertyLookup;
	}

	/**
	 * Create a new PropertyLookup instance
	 *
	 * @return PropertyLookup
	 */
	protected function newPropertyLookup() {
		$entityLookup = $this->getEntityLookup();

		return new PropertySQLLookup( $entityLookup );
	}

	/**
	 * @see Store::newChangesTable
	 *
	 * @since 0.4
	 *
	 * @return ChangesTable
	 */
	public function newChangesTable() {
		return new ChangesTable( $this->repoWiki );
	}

	/**
	 * Does nothing.
	 *
	 * @since 0.3
	 */
	public function clear() {
		// noop
	}

	/**
	 * Does nothing.
	 *
	 * @since 0.3
	 */
	public function rebuild() {
		$this->clear();
	}

}
