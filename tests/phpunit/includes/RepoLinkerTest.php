<?php

namespace Wikibase\Test;

use Wikibase\DataModel\Entity\ItemId;
use Wikibase\RepoLinker;

/**
 * @covers Wikibase\RepoLinker
 *
 * @since 0.4
 *
 * @group WikibaseClient
 * @group RepoLinkerTest
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class RepoLinkerTest extends \PHPUnit_Framework_TestCase {

	protected function getRepoSettings() {
		return array(
			array(
				'baseUrl' => '//www.example.com',
				'articlePath' => '/wiki/$1',
				'scriptPath' => '',
				'repoNamespaces' => array(
					'wikibase-item' => '',
					'wikibase-property' => 'Property'
				)
			),
			array(
				'baseUrl' => '//example.com/',
				'articlePath' => '/wiki/$1',
				'scriptPath' => '',
				'repoNamespaces' => array(
					'wikibase-item' => '',
					'wikibase-property' => 'Property'
				)
			),
			array(
				'baseUrl' => 'http://www.example.com',
				'articlePath' => '/wiki/$1',
				'scriptPath' => '/w',
				'repoNamespaces' => array(
					'wikibase-item' => 'Item',
					'wikibase-property' => 'Property'
				)
			)
		);
	}

	protected function getRepoLinkerForSettings( $settings ) {
		return new RepoLinker(
			$settings['baseUrl'],
			$settings['articlePath'],
			$settings['scriptPath'],
			$settings['repoNamespaces']
		);
	}

	/**
	 * @dataProvider namespaceProvider
	 */
	public function testGetNamespace( $expected, array $settings, $entityType ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );
		$namespace = $repoLinker->getNamespace( $entityType );

		$this->assertEquals( $expected, $namespace );
	}

	public function namespaceProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'',
				$settings[0],
				'item'
			),
			array(
				'Property',
				$settings[1],
				'property'
			),
			array(
				'Item',
				$settings[2],
				'item'
			)
		);
	}

	/**
	 * @dataProvider getPageUrlProvider
	 */
	public function testGetPageUrl( $expected, $settings, $page ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->getPageUrl( $page ) );
	}

	public function getPageUrlProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array( '//www.example.com/wiki/Cat', $settings[0], 'Cat' ),
			array( 'http://www.example.com/wiki/Frog', $settings[2], 'Frog' )
		);
	}

	/**
	 * @dataProvider formatLinkProvider
	 */
	public function testFormatLink( $expected, $settings, $url, $text, $attribs ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->formatLink( $url, $text, $attribs ) );
	}

	public function formatLinkProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'<a class="plainlinks" href="//example.com/wiki/Special:Log/delete">delete</a>',
				$settings[1],
				'//example.com/wiki/Special:Log/delete',
				'delete',
				null
			),
			array(
				'<a class="plainlinks" tabindex="1" href="http://www.example.com/w/index.php'
					. '?title=Q730&amp;diff=prev&amp;oldid=778">diff</a>',
				$settings[2],
				'http://www.example.com/w/index.php?title=Q730&diff=prev&oldid=778',
				'diff',
				array(
					'class' => 'plainlinks',
					'tabindex' => 1
				)
			)
		);
	}

	/**
	 * @dataProvider buildEntityLinkProvider
	 */
	public function testBuildEntityLink( $expected, $settings, $entityId, $classes ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->buildEntityLink( $entityId, $classes ) );
	}

	public function buildEntityLinkProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'<a class="plainlinks wb-entity-link" href="//example.com/wiki/Q730">Q730</a>',
				$settings[1],
				new ItemId( 'Q730' ),
				array()
			),
			array(
				'<a class="plainlinks wb-entity-link" href="http://www.example.com/wiki/Item:Q730">Q730</a>',
				$settings[2],
				new ItemId( 'Q730' ),
				array()
			),
			array(
				'<a class="plainlinks wb-entity-link kittens" href="http://www.example.com/wiki/Item:Q730">Q730</a>',
				$settings[2],
				new ItemId( 'Q730' ),
				array( 'kittens' )
			)
		);
	}

	/**
	 * @dataProvider getEntityUrlProvider
	 */
	public function testGetEntityUrl( $expected, $settings, $entityId ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->getEntityUrl( $entityId ) );
	}

	public function getEntityUrlProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'//example.com/wiki/Q730',
				$settings[1],
				new ItemId( 'Q730' )
			),
			array(
				'http://www.example.com/wiki/Item:Q1234',
				$settings[2],
				new ItemId( 'Q1234' )
			)
		);
	}

	/**
	 * @dataProvider getBaseUrlProvider
	 */
	public function testGetBaseUrl( $expected, $settings ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->getBaseUrl() );
	}

	public function getBaseUrlProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'http://www.example.com',
				$settings[2]
			),
			array(
				'//example.com',
				$settings[1]
			)
		);
	}

	/**
	 * @dataProvider getApiUrlProvider
	 */
	public function testGetApiUrl( $expected, $settings ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->getApiUrl() );
	}

	public function getApiUrlProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'http://www.example.com/w/api.php',
				$settings[2]
			)
		);
	}

	/**
	 * @dataProvider getIndexUrlProvider
	 */
	public function testGetIndexUrl( $expected, $settings ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->getIndexUrl() );
	}

	public function getIndexUrlProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'http://www.example.com/w/index.php',
				$settings[2]
			)
		);
	}

	/**
	 * @dataProvider addQueryParamsProvider
	 */
	public function testAddQueryParams( $expected, $settings, $url, $params ) {
		$repoLinker = $this->getRepoLinkerForSettings( $settings );

		$this->assertEquals( $expected, $repoLinker->addQueryParams( $url, $params ) );
	}

	public function addQueryParamsProvider() {
		$settings = $this->getRepoSettings();

		return array(
			array(
				'http://www.example.com/w/api.php?action=query&prop=revisions&titles=Q60',
				$settings[2],
				'http://www.example.com/w/api.php',
				array(
					'action' => 'query',
					'prop' => 'revisions',
					'titles' => 'Q60'
				)
			)
		);
	}
}
