<?php

namespace Wikibase\Test;

use Title;
use Wikibase\Client\Specials\SpecialUnconnectedPages;
use Wikibase\NamespaceChecker;

/**
 * @covers Wikibase\Client\Specials\SpecialUnconnectedPages
 *
 * @since 0.4
 *
 * @group WikibaseClient
 * @group SpecialPage
 * @group WikibaseSpecialPage
 * @group WikibaseSpecialUnconnectedPages
 * @group Wikibase
 *
 * @licence GNU GPL v2+
 * @author John Erling Blad < jeblad@gmail.com >
 */
class SpecialUnconnectedPagesTest extends SpecialPageTestBase {

	protected function newSpecialPage() {
		return new SpecialUnconnectedPages();
	}

	public function testExecute(  ) {
		//TODO: Actually verify that the output is correct.
		//      Currently this just tests that there is no fatal error.
		list( $output, ) = $this->executeSpecialPage( '' );
		$this->assertTrue( true, 'Calling execute without any subpage value' );
	}

	/**
	 * @dataProvider provideNamespaceChecker
	 */
	public function testNamespaceChecker( $namespace, $expected ) {
		$page = $this->newSpecialPage();
		$checker = new NamespaceChecker( array( 2, 4 ), array( 0 ) );
		$page->setNamespaceChecker( $checker );
		$this->assertEquals( $expected, $page->getNamespaceChecker()->isWikibaseEnabled( $namespace ) );
	}

	public function provideNamespaceChecker() {
		return array(
			array( 0, true ),  // #0
			array( 1, false ), // #1
			array( 2, false ), // #2
			array( 3, false ), // #3
			array( 4, false ), // #4
			array( 5, false ), // #5
			array( 6, false ), // #6
			array( 7, false ), // #7
		);
	}

	/**
	 *  @dataProvider provideBuildConditionals
	 */
	public function testBuildConditionals( $text, $expected ) {
		$page = $this->newSpecialPage();
		$title = Title::newFromText( $text);
		$checker = new NamespaceChecker( array( 2, 4 ), array( 0 ) );
		$dbr = wfGetDB( DB_SLAVE );
		$this->assertEquals( $expected, $page->buildConditionals( $dbr, $title, $checker ) );
	}

	public function provideBuildConditionals() {
		return array(
			array( 'foo', array( "page_title >= 'Foo'", "page_namespace = 0", 'page_namespace IN (0)' ) ),
			array( ':foo', array( "page_title >= 'Foo'", "page_namespace = 0", 'page_namespace IN (0)' ) ),
			array( 'user:foo', array( "page_title >= 'Foo'", "page_namespace = 2", 'page_namespace IN (0)' ) ),
			array( 'user talk:foo', array( "page_title >= 'Foo'", "page_namespace = 3", 'page_namespace IN (0)' ) ),
		);
	}

}
