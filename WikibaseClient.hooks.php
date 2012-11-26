<?php
namespace Wikibase;

/**
 * File defining the hook handlers for the Wikibase Client extension.
 *
 * @since 0.1
 *
 * @file
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 *
 * @author Katie Filbert < aude.wiki@gmail.com >
 * @author Jens Ohlig
 * @author Daniel Kinzler
 * @author Tobias Gritschacher
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
	 * @return bool
	 */
	public static function onSchemaUpdate( \DatabaseUpdater $updater ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$type = $updater->getDB()->getType();

		if ( $type === 'mysql' || $type === 'sqlite' /* || $type === 'postgres' */ ) {
			$extension = $type === 'postgres' ? '.pg.sql' : '.sql';

			$updater->addExtensionTable(
				'wbc_entity_cache',
				__DIR__ . '/sql/WikibaseCache' . $extension
			);

			$updater->addExtensionTable(
				'wbc_item_usage',
				__DIR__ . '/sql/KillLocalItems.sql'
			);

			$updater->addExtensionTable(
				'wbc_item_usage',
				__DIR__ . '/sql/WikibaseClient' . $extension
			);
		}
		else {
			wfWarn( "Database type '$type' is not supported by Wikibase Client." );
		}

		wfProfileOut( "Wikibase-" . __METHOD__ );
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
	 * @return bool
	 */
	public static function registerUnitTests( array &$files ) {
		// @codeCoverageIgnoreStart
		$testFiles = array(
			'includes/CachedEntity',
			'includes/EntityCacheUpdater',

			'includes/store/EntityCacheTable',
		);

		foreach ( $testFiles as $file ) {
			$files[] = __DIR__ . '/tests/phpunit/' . $file . 'Test.php';
		}

		return true;
		// @codeCoverageIgnoreEnd
	}

	/**
	 * Deletes all the data stored on the repository.
	 *
	 * @since 0.2
	 *
	 * @param callable $reportMessage
	 *
	 * @return bool
	 */
	public static function onWikibaseDeleteData( $reportMessage ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$store = ClientStoreFactory::getStore();
		$stores = array_flip( $GLOBALS['wgWBClientStores'] );

		$reportMessage( "Deleting data from the " . $stores[get_class( $store )] . " store..." );

		$store->clear();

		$reportMessage( "done!\n" );

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Rebuilds all the data stored on the repository.
	 * This hook will probably be called manually when the
	 * rebuildAllData script is run on the client.
	 * @todo Be smarter and call this hook from pollForChanges
	 *
	 * @since 0.2
	 *
	 * @param callable $reportMessage
	 *
	 * @return bool
	 */
	public static function onWikibaseRebuildData( $reportMessage ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$store = ClientStoreFactory::getStore();
		$stores = array_flip( $GLOBALS['wgWBClientStores'] );
		$reportMessage( "Rebuilding all data in the " . $stores[get_class( $store )] . " store on the client..." );
		$store->rebuild();
		$changes = ChangesTable::singleton();
		$changes = $changes->select(
			null,
			array(),
			array(),
			__METHOD__
		);
		ChangeHandler::singleton()->handleChanges( iterator_to_array( $changes ) );
		$reportMessage( "done!\n" );

		wfProfileOut( "Wikibase-" . __METHOD__ );
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
	 * @return bool
	 */
	public static function onWikibasePollHandle( Change $change ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		if ( ! ( $change instanceof EntityChange ) ) {
			return true;
		}

		$cacheUpdater = new EntityCacheUpdater();
		$cacheUpdater->handleChange( $change );

		if ( ! ( $change instanceof ItemChange ) ) {
			//TODO: other kinds of changes need entirely different kind of handling.
			return true;
		}

		/**
		 * @var Item $item
		 */
		$item = $change->getEntity();
		$siteGlobalId = Settings::get( 'siteGlobalID' );
		$title = null;

		$pagesToUpdate = array();

		// if something relevant about the entity changes, update
		// the corresponding local page
		if ( ClientHooks::changeNeedsRendering( $change ) ) {
			$siteLink = $item->getSiteLink( $siteGlobalId );

			if ( $siteLink ) {
				// we have a corresponding page on this wiki, so re-render it.
				$pagesToUpdate[] = $siteLink->getPage();
			}
		}

		// if an item's sitelinks change, update the old and the new target
		$siteLinkDiff = $change->getSiteLinkDiff();
		$siteLinkDiffOp = isset( $siteLinkDiff[ $siteGlobalId ] )
								? $siteLinkDiff[ $siteGlobalId ] : null;

		if ( $siteLinkDiffOp === null ) {
			// no op
		} elseif ( $siteLinkDiffOp instanceof \Diff\DiffOpAdd ) {
			$pagesToUpdate[] = $siteLinkDiffOp->getNewValue(); // currently redundant, but keep for future reference
		} elseif ( $siteLinkDiffOp instanceof \Diff\DiffOpRemove ) {
			$pagesToUpdate[] = $siteLinkDiffOp->getOldValue();
		} elseif ( $siteLinkDiffOp instanceof \Diff\DiffOpChange ) {
			$pagesToUpdate[] = $siteLinkDiffOp->getNewValue(); // currently redundant, but keep for future reference
			$pagesToUpdate[] = $siteLinkDiffOp->getOldValue();
		} else {
			wfWarn( "Unknown change operation: " . get_class( $siteLinkDiffOp )
					. " (" . $siteLinkDiffOp->getType() . ")" );
		}

		// purge all relevant pages
		//
		// @todo: instead of rerendering everything, schedule pages for different kinds
		// of update, depending on how the entity was modified. E.g. changes to
		// sitelinks could in theory be handled without re-parsing the page, but
		// would still need to purge the squid cache.
		foreach ( array_unique( $pagesToUpdate ) as $page ) {
			self::updatePage( $page, $change, false );
		}

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * @static
	 *
	 * @todo: move this somewhere same
	 *
	 * @param Change $change
	 *
	 * @return bool
	 */
	public static function changeNeedsRendering( ItemChange $change ) {
		if ( !$change->getSiteLinkDiff()->isEmpty() ) {
			return true;
		}

		return false;
	}

	/**
	 * Registers change with recent changes and performs other updates
	 *
	 * @since 0.2
	 *
	 * @param string $title  The Title of the page to update
	 * @param Change $change The Change that caused the update
	 *
	 * @return bool
	 */
	protected static function updatePage( $titleText, Change $change ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$title = \Title::newFromText( $titleText );

		if ( !$title->exists() ) {
			wfProfileOut( "Wikibase-" . __METHOD__ );
			return false;
		}

		$title->invalidateCache();
		$title->purgeSquid();

		if ( Settings::get( 'injectRecentChanges' )  === false ) {
			wfProfileOut( "Wikibase-" . __METHOD__ );
			return true;
		}

		$rcinfo = $change->getMetadata();

		if ( ! is_array( $rcinfo ) ) {
			wfProfileOut( "Wikibase-" . __METHOD__ );
			return false;
		}

		$fields = $change->getFields(); //@todo: Fixme: add getFields() to the interface, or provide getters!
		$fields['entity_type'] = $change->getEntityType();
		unset( $fields['info'] );

		$params = array(
			'wikibase-repo-change' => array_merge( $fields, $rcinfo )
		);

		$rc = ExternalRecentChange::newFromAttribs( $params, $title );

		// todo: avoid reporting the same change multiple times when re-playing repo changes! how?!
		$rc->save();

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Hook for injecting a message on [[Special:MovePage]]
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SpecialMovepageAfterMove
	 *
	 * @since 0.3
	 *
	 * @param \MovePageForm $movePage
	 * @param \Title &$oldTitle
	 * @param \Title &$newTitle
	 *
	 * @return bool
	 */
	public static function onSpecialMovepageAfterMove( \MovePageForm $movePage, \Title &$oldTitle, \Title &$newTitle ) {
		$siteLinkCache = ClientStoreFactory::getStore()->newSiteLinkCache();
		$globalId = Settings::get( 'siteGlobalID' );
		$itemId = $siteLinkCache->getItemIdForLink(
			$globalId,
			$oldTitle->getText()
		);

		if ( $itemId !== false ) {
			$itemByTitle = 'Special:ItemByTitle/' . $globalId . '/' . $oldTitle->getDBkey();
			$itemByTitleLink = ClientUtils::baseUrl() . $itemByTitle;
			$out = $movePage->getOutput();
			$out->addModules( 'wikibase.client.page-move' );
			$out->addHTML(
				\Html::rawElement(
					'div',
					array( 'id' => 'wbc-after-page-move',
							'class' => 'plainlinks' ),
					wfMessage( 'wbc-after-page-move', $itemByTitleLink )->parse()
				)
			);
		}
		return true;
	}

	/**
	 * Hook for modifying the query for fetching recent changes
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SpecialRecentChangesQuery
	 *
	 * @since 0.2
	 *
	 * @param &$conds[]
	 * @param &$tables[]
	 * @param &$join_conds[]
	 * @param \FormOptions $opts
	 * @param &$query_options[]
	 * @param &$fields[]
	 *
	 * @return bool
	 */
	public static function onSpecialRecentChangesQuery( array &$conds, array &$tables, array &$join_conds,
		\FormOptions $opts, array &$query_options, array &$fields ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		if ( Settings::get( 'showExternalRecentChanges' ) === false || $opts->getValue( 'hidewikidata' ) === true ) {
			$conds[] = 'rc_type != ' . RC_EXTERNAL;
		}

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Hook for formatting recent changes linkes
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/OldChangesListRecentChangesLine
	 *
	 * @since 0.2
	 *
	 * @param \ChangesList $changesList
	 * @param string $s
	 * @param \RecentChange $rc
	 *
	 * @return bool
	 */
	public static function onOldChangesListRecentChangesLine( \ChangesList &$changesList, &$s, \RecentChange $rc ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$rcType = $rc->getAttribute( 'rc_type' );
		if ( $rcType == RC_EXTERNAL ) {
			$params = unserialize( $rc->getAttribute( 'rc_params' ) );
			if ( array_key_exists( 'wikibase-repo-change', $params ) ) {
				$line = ExternalChangesList::changesLine( $changesList, $rc );
				if ( $line == false ) {
					return false;
				}
				$s = $line;
			}
		}

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Modifies watchlist query to include external changes
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SpecialWatchlistQuery
	 *
	 * @since 0.2
	 *
	 * @param array &$conds
	 * @param array &$tables
	 * @param array &$join_conds
	 * @param array &$fields
	 *
	 * @return bool
	 */
	public static function onSpecialWatchlistQuery( array &$conds, array &$tables, array &$join_conds, array &$fields ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$newConds = array();
		foreach( $conds as $k => $v ) {
			if ( $v ===  'rc_this_oldid=page_latest OR rc_type=3' ) {
				$newConds[$k] = 'rc_this_oldid=page_latest OR rc_type=3 OR rc_type=5';
			} else {
				$newConds[$k] = $v;
			}
		}
		$conds = $newConds;

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Hook runs after internal parsing
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserAfterParse
	 *
	 * @since 0.1
	 *
	 * @param \Parser $parser
	 * @param string $text
	 * @param \StripState $stripState
	 *
	 * @return bool
	 */
	public static function onParserAfterParse( \Parser &$parser, &$text, \StripState $stripState ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );
		global $wgLanguageCode;

		$parserOutput = $parser->getOutput();

		// only run this once, for the article content and not interface stuff
		if ( ! $parser->getOptions()->getInterfaceMessage() ) {
			$useRepoLinks = LangLinkHandler::useRepoLinks( $parser );

			if ( $useRepoLinks ) {

				$repoLinkItems = array();

				$repoLinks = LangLinkHandler::getEntityCacheLinks( $parser );

				if ( count( $repoLinks ) > 0 ) {
					LangLinkHandler::suppressRepoLinks( $parser, $repoLinks );

					/**
					 * @var SiteLink $link
					 */
					foreach ( $repoLinks as $link ) {
						foreach ( $link->getSite()->getNavigationIds() as $navigationId ) {
							if ( $navigationId !== $wgLanguageCode ) {
								$repoLinkItems[] = $navigationId . ':' . $link->getPage();
							}
						}
					}
				}

				// get interwiki lang links from local wikitext
				$localLinks = $parserOutput->getLanguageLinks();

				// building array with language codes from local links
				$localLinkCodes = array();
				foreach ( $localLinks as $localLink ) {
					$matches = array();
					preg_match( "/([^:]*):/", $localLink, $matches );
					$localLinkCodes[] = $matches[1];
				}

				// use the local links as default and add missing links from the repo
				foreach ( $repoLinkItems as $repoLink ) {
					$matches = array();
					preg_match( "/([^:]*):/", $repoLink, $matches );
					if ( !in_array( $matches[1], $localLinkCodes ) ) {
						$parserOutput->addLanguageLink( $repoLink );
					}
				}
			}

			if ( $useRepoLinks || Settings::get( 'alwaysSort' ) ) {
				// sort links
				SortUtils::sortLinks( $parserOutput->getLanguageLinks() );
			}
		}

		wfProfileOut( "Wikibase-" . __METHOD__ );
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
	 * @return bool
	 */
	public static function onWikibaseDefaultSettings( array &$settings ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$settings = array_merge(
			$settings,
			array(
				'namespaces' => array( NS_MAIN ),
				'source' => array( 'dir' => __DIR__ . '/tests' ),
				// temporary hack to provide default settings
				'repoBase' => 'http://wikidata-test-repo.wikimedia.de/wiki/',
				'repoApi' => 'http://wikidata-test-repo.wikimedia.de/w/api.php',
				'sort' => 'code',
				'sortPrepend' => false,
				'alwaysSort' => true,
				'siteGlobalID' => 'enwiki',
				'siteGroup' => 'wikipedia',
				'injectRecentChanges' => true,
				'showExternalRecentChanges' => true,
				'defaultClientStore' => 'sqlstore',
				// default for repo items in main namespace
				'repoNamespaces' => array(
					'wikibase-item' => '',
					'wikibase-property' => 'Property'
				)
			)
		);

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Adds css for the edit links sidebar link
	 *
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public static function onBeforePageDisplay( \OutputPage $out, \Skin $skin ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		$title = $out->getTitle();

		if ( in_array( $title->getNamespace(), Settings::get( 'namespaces' ) ) ) {
			$out->addModules( 'wikibase.client.init' );
		}

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Displays a list of links to pages on the central wiki at the end of the language box.
	 *
	 * @param \Skin $skin
	 * @param \QuickTemplate $template
	 *
	 * @since 0.1
	 *
	 * @return bool
	 */
	public static function onSkinTemplateOutputPageBeforeExec( \Skin &$skin, \QuickTemplate &$template ) {
		wfProfileIn( "Wikibase-" . __METHOD__ );

		if ( empty( $template->data['language_urls'] ) ) {
			wfProfileOut( "Wikibase-" . __METHOD__ );
			return true;
		}

		$title = $skin->getContext()->getTitle();
		if ( in_array( $title->getNamespace(), Settings::get( 'namespaces' ) ) ) {

			$editUrl = Settings::get( 'repoBase' );
			if( !$editUrl ) {
				wfProfileOut( "Wikibase-" . __METHOD__ );
				return true;
			}

			$title = $skin->getContext()->getTitle();

			// gets the main part of the title, no underscores used in this db table
			$titleText = $title->getText();

			// main part of title for building link
			$titleLink = $title->getPartialURL();
			$siteId = Settings::get( 'siteGlobalID' );

			$itemId = ClientStoreFactory::getStore()->newSiteLinkCache()->getItemIdForLink( $siteId, $titleText );

			if ( $itemId ) {
				// links to the special page
				$template->data['language_urls'][] = array(
					'href' => rtrim( $editUrl, "/" ) . "/Special:ItemByTitle/$siteId/$titleLink",
					'text' => wfMessage( 'wbc-editlinks' )->text(),
					'title' => wfMessage( 'wbc-editlinkstitle' )->text(),
					'class' => 'wbc-editpage',
				);
			}
		}

		wfProfileOut( "Wikibase-" . __METHOD__ );
		return true;
	}

	/**
	 * Adds a toggle for showing/hiding Wikidata entries in recent changes
	 *
	 * @param \SpecialRecentChanges $special
	 * @param array &$filters
	 *
	 * @return bool
	 */
	public static function onSpecialRecentChangesFilters( \SpecialRecentChanges $special, array &$filters ) {
		$showWikidata = $special->getUser()->getOption( 'rcshowwikidata' );
		$default = $showWikidata ? false : true;
		$filters['hidewikidata'] = array( 'msg' => 'wbc-rc-hide-wikidata', 'default' => $default );
		return true;
	}

	/**
	 * Adds a preference for showing or hiding Wikidata entries in recent changes
	 *
	 * @param \User $user
	 * @param $preferences[]
	 *
	 * @return bool
	 */
	public static function onGetPreferences( \User $user, array $prefs ) {
		$prefs['rcshowwikidata'] = array(
			'type' => 'toggle',
			'label-message' => 'wbc-rc-show-wikidata-pref',
			'section' => 'rc/advancedrc',
		);
		return true;
	}

}
