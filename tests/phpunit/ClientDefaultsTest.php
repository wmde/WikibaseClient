<?php
 /**
 *
 * Copyright © 02.07.13 by the authors listed below.
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
 * @license GPL 2+
 * @file
 *
 * @ingroup WikibaseClient
 * @ingroup Test
 *
 * @group Wikibase
 * @group WikibaseClient
 *
 * @author Daniel Kinzler
 */


namespace Wikibase\Test;


use Wikibase\SettingsArray;

/**
 * Class ClientDefaultsTest
 * @package Wikibase\Test
 */
class ClientDefaultsTest extends \MediaWikiTestCase {

	public static function settingsProvider() {
		//TODO: repoDatabase
		//TODO: changesDatabase
		//TODO: sharedCacheKeyPrefix

		return array(
			array( // #0: no local repo, all values set
				array( // $settings
					'repoUrl' => 'http://acme.com',
					'repoArticlePath' => '/wiki',
					'repoScriptPath' => '/w',
					'siteGlobalID' => 'mywiki',
					'repoDatabase' => 'foo',
					'changesDatabase' => 'doo',
					'sharedCacheKeyPrefix' => 'xoo:WBL/' . WBL_VERSION,
				),
				array( // $wg
					'wgServer' => 'http://www.acme.com',
					'wgArticlePath' => '/mywiki',
					'wgScriptPath' => '/mediawiki',
					'wgDBname' => 'mw_mywiki',
				),
				false, // $repoIsLocal
				array( // $expected
					'repoUrl' => 'http://acme.com',
					'repoArticlePath' => '/wiki',
					'repoScriptPath' => '/w',
					'siteGlobalID' => 'mywiki',
					'repoDatabase' => 'foo',
					'changesDatabase' => 'doo',
					'sharedCacheKeyPrefix' => 'xoo:WBL/' . WBL_VERSION,
				)
			),

			array( // #1: no local repo, no values set
				array( // $settings
				),
				array( // $wg
					'wgServer' => 'http://www.acme.com',
					'wgArticlePath' => '/mywiki',
					'wgScriptPath' => '/mediawiki',
					'wgDBname' => 'mw_mywiki',
				),
				false, // $repoIsLocal
				array( // $expected
					'repoUrl' => '//www.wikidata.org',   // hardcoded default
					'repoArticlePath' => '/wiki/$1', // hardcoded default
					'repoScriptPath' => '/w', // hardcoded default
					'siteGlobalID' => 'mw_mywiki',
					'repoDatabase' => null,
					'changesDatabase' => null,
					'sharedCacheKeyPrefix' => 'repoUrl://www.wikidata.org:WBL/' . WBL_VERSION,
				)
			),

			array( // #2: local repo, all values set
				array( // $settings
					'repoUrl' => 'http://acme.com',
					'repoArticlePath' => '/wiki',
					'repoScriptPath' => '/w',
					'siteGlobalID' => 'mywiki',
					'repoDatabase' => 'foo',
					'changesDatabase' => 'doo',
					'sharedCacheKeyPrefix' => 'foo:WBL/' . WBL_VERSION,
				),
				array( // $wg
					'wgServer' => 'http://www.acme.com',
					'wgArticlePath' => '/mywiki',
					'wgScriptPath' => '/mediawiki',
					'wgDBname' => 'mw_mywiki',
				),
				true, // $repoIsLocal
				array( // $expected
					'repoUrl' => 'http://acme.com',
					'repoArticlePath' => '/wiki',
					'repoScriptPath' => '/w',
					'siteGlobalID' => 'mywiki',
					'repoDatabase' => 'foo',
					'changesDatabase' => 'doo',
					'sharedCacheKeyPrefix' => 'foo:WBL/' . WBL_VERSION,
				)
			),

			array( // #3: local repo, no values set
				array( // $settings
				),
				array( // $wg
					'wgServer' => 'http://www.acme.com',
					'wgArticlePath' => '/mywiki',
					'wgScriptPath' => '/mediawiki',
					'wgDBname' => 'mw_mywiki',
				),
				true, // $repoIsLocal
				array( // $expected
					'repoUrl' => 'http://www.acme.com',
					'repoArticlePath' => '/mywiki',
					'repoScriptPath' => '/mediawiki',
					'siteGlobalID' => 'mw_mywiki',
					'repoDatabase' => false,
					'changesDatabase' => false,
					'sharedCacheKeyPrefix' => 'mw_mywiki:WBL/' . WBL_VERSION,
				)
			),

			array( // #4: derive changesDatabase
				array( // $settings
					'repoDatabase' => 'mw_foowiki',
				),
				array( // $wg
				),
				false, // $repoIsLocal
				array( // $expected
					'repoDatabase' => 'mw_foowiki',
					'changesDatabase' => 'mw_foowiki',
				)
			),

			array( // #5: derive sharedCacheKeyPrefix from repoDatabase value
				array( // $settings
					'repoDatabase' => 'mw_foowiki',
					'repoUrl' => 'http://www.acme.com',
				),
				array( // $wg
					'wgDBname' => 'mw_mywiki',
				),
				false, // $repoIsLocal
				array( // $expected
					'sharedCacheKeyPrefix' => 'mw_foowiki:WBL/' . WBL_VERSION,
				)
			),

			array( // #6: derive sharedCacheKeyPrefix from repoUrl
				array( // $settings
					'repoDatabase' => null,
					'repoUrl' => 'http://www.acme.com',
				),
				array( // $wg
					'wgDBname' => 'mw_mywiki',
				),
				false, // $repoIsLocal
				array( // $expected
					'sharedCacheKeyPrefix' => 'repoUrl:http://www.acme.com:WBL/' . WBL_VERSION,
				)
			),

			array( // #7: derive sharedCacheKeyPrefix from wgDBname
				array( // $settings
					'repoDatabase' => false,
					'repoUrl' => 'http://www.acme.com',
				),
				array( // $wg
					'wgDBname' => 'mw_mywiki',
				),
				false, // $repoIsLocal
				array( // $expected
					'sharedCacheKeyPrefix' => 'mw_mywiki:WBL/' . WBL_VERSION,
				)
			),
		);
	}

	/**
	 * @dataProvider settingsProvider
	 */
	public function testDefaults( array $settings, array $wg, $repoIsLocal, $expected ) {
		$this->setMwGlobals( $wg );

		$defaults = include( WBC_DIR . '/config/WikibaseClient.default.php' );

		$settings = array_merge( $defaults, $settings );
		$settings = new SettingsArray( $settings );

		//NOTE: thisWikiIsTheRepo us used by some "magic" (dynamic) defaults
		//      to decide how to behave. Normally, this is true if and only if
		//      WB_VERSION is defined.
		$settings->setSetting( 'thisWikiIsTheRepo', $repoIsLocal );

		foreach ( $expected as $key => $exp ) {
			$actual = $settings->getSetting( $key );
			$this->assertSame( $exp, $actual, "Setting $key" );
		}
	}

}