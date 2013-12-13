<?php

namespace Wikibase\Test;

use Language;
use Scribunto_LuaWikibaseLibraryImplementation;
use Wikibase\Client\WikibaseClient;

/**
 * @covers Scribunto_LuaWikibaseLibraryImplementation
 *
 * @since 0.4
 *
 * @group Wikibase
 * @group WikibaseClient
 * @group Scribunto_LuaWikibaseLibraryImplementationTest
 *
 * @licence GNU GPL v2+
 * @author Jens Ohlig < jens.ohlig@wikimedia.de >
 */
class Scribunto_LuaWikibaseLibraryImplementationTest extends \PHPUnit_Framework_TestCase {

	public function getWikibaseLibraryImplementation() {
		$entityLookup = new MockRepository();
		$language = new Language( "en" );
		return new Scribunto_LuaWikibaseLibraryImplementation(
			WikibaseClient::getDefaultInstance()->getEntityIdParser(), // EntityIdParser
			$entityLookup,
			WikibaseClient::getDefaultInstance()->getEntityIdFormatter(), // EntityIdFormatter
			WikibaseClient::getDefaultInstance()->getStore()->getSiteLinkTable(), // SiteLinkLookup
			$language, // language
			"enwiki" // siteId
		);
	}

	/**
	 * @dataProvider provideEntity
	 */
	public function testGetEntity( $entity ) {
		$entityArr = $this->getWikibaseLibraryImplementation()->getEntity( $entity );
		$this->assertInternalType( 'array', $entityArr );
	}

	public function provideEntity() {
		return array( array( 'q42' ), array( 'q23' ) );
	}
}
