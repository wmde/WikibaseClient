<?php

namespace Wikibase;
use DatabaseUpdater;

/**
 * File defining the hook handlers for the Wikibase Client extension.
 *
 * @since 0.1
 *
 * @file WikibaseClient.hooks.php
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class ClientHooks {

	/**
	 * Schema update to set up the needed database tables.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/LoadExtensionSchemaUpdates
	 *
	 * @since 0.1
	 *
	 * @param \DatabaseUpdater $updater
	 *
	 * @return boolean
	 */
	public static function onSchemaUpdate( DatabaseUpdater $updater ) {
		$type = $updater->getDB()->getType();

		if ( $type === 'mysql' || $type === 'sqlite' ) {
			$updater->addExtensionTable(
				'wbc_local_items',
				dirname( __FILE__ ) . '/sql/WikibaseClient.sql'
			);

			$updater->addExtensionField(
				'wbc_local_items',
				'li_page_title',
				dirname( __FILE__ ) . '/sql/LocalItemTitleField.sql'
			);
		}
		elseif ( $type === 'postgres' ) {
			$updater->addExtensionTable(
				'wbc_local_items',
				dirname( __FILE__ ) . '/sql/WikibaseClient.pg.sql'
			);
		}
		else {
			// TODO
		}

		return true;
	}

	/**
	 * Hook to add PHPUnit test cases.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/UnitTestsList
	 *
	 * @since 0.1
	 *
	 * @param array $files
	 *
	 * @return boolean
	 */
	public static function registerUnitTests( array &$files ) {
		$testFiles = array(
			'General',
			'Sorting',

			'includes/LocalItemsTable',
			'includes/LocalItem',
		);

		foreach ( $testFiles as $file ) {
			$files[] = dirname( __FILE__ ) . '/tests/phpunit/' . $file . 'Test.php';
		}

		return true;
	}

	/**
	 * When the poll script finds a new change or set of changes, it will fire
	 * this hook for each change, so it can be handled appropriately.
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/WikibasePollHandle
	 *
	 * @since 0.1
	 *
	 * @param Change $change
	 *
	 * @return boolean
	 */
	public static function onWikibasePollHandle( Change $change ) {
		list( $mainType, ) = explode( '-', $change->getType() );

		if ( $mainType === 'item' ) {
			$itemUpdater = new ItemUpdater();
			$itemUpdater->handleChange( $change );
		}

		return true;
	}

	/**
	 * Used to process the nearly-rendered html code for the page (but before any html tidying occurs).
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserBeforeTidy
	 *
	 * This is used to add the remote navigation links to the navigation menu.
	 * TODO: this might not be a good place to do this - ParserAfterParse ?
	 *
	 * @since 0.1
	 *
	 * @param \Parser $parser
	 * @param string $text
	 *
	 * @return boolean
	 */
	public static function onParserBeforeTidy( \Parser &$parser, &$text ) {
		$parserOutput = $parser->getOutput();

		$localItem = LocalItemsTable::singleton()->selectRow( null, array( 'page_title' => $parser->getTitle()->getDBkey() ) );

		if ( $localItem !== false ) {
			/**
			 * @var LocalItem $localItem
			 * @var SiteLink $link
			 */
			foreach ( $localItem->getItem()->getSiteLinks() as $link ) {
				// TODO: hold into account wiki-wide and page-specific settings to do the merge rather then just overriding.
				$parserOutput->addLanguageLink( $link->getSite()->getGlobalId() . ':' . $link->getPage() );
			}
		}

		return true;
	}

	/**
	 * Adds default settings.
	 * Setting name (string) => setting value (mixed)
	 *
	 * @param array &$settings
	 *
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public static function onWikibaseDefaultSettings( array &$settings ) {
		$settings = array_merge(
			$settings,
			array(
				'namespaces' => array( NS_MAIN ),
				'source' => array( 'dir' => dirname(__FILE__) . '/tests' ),
				'editURL' => '',
				'sort' => 'none',
				'sortPrepend' => false,
				'alwaysSort' => false,
			)
		);

		return true;
	}

}
