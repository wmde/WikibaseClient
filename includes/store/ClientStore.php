<?php

namespace Wikibase;

/**
 * Client store interface.
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
 * @since 0.1
 *
 * @todo: provide getXXX() methods for getting local pseudo-singletons (shared service objects).
 *
 * @file
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface ClientStore {

	/**
	 * Returns a new SiteLinkLookup for this store.
	 *
	 * @since 0.1
	 *
	 * @return SiteLinkLookup
	 */
	public function newSiteLinkTable();

	/**
	 * Returns a new EntityLookup for this store.
	 *
	 * @since 0.1
	 *
	 * @return EntityLookup
	 *
	 * @todo: rename to newEntityMirror
	 */
	public function getEntityLookup();

	/**
	 * Returns a new PropertyLookup
	 *
	 * @since 0.4
	 *
	 * @return PropertyLookup
	 */
	public function getPropertyLookup();

	/**
	 * Returns a new ChangesTable for this store.
	 *
	 * @since 0.4
	 *
	 * @return ChangesTable
	 *
	 * @throws \MWException if no changes table can be supplied.
	 */
	public function newChangesTable();

	/**
	 * Removes all data from the store.
	 *
	 * @since 0.2
	 */
	public function clear();

	/**
	 * Rebuilds all data in the store.
	 *
	 * @since 0.2
	 */
	public function rebuild();
}
