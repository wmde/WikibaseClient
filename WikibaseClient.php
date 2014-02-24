<?php
/**
 * Initialization file for the Wikibase Client extension.
 *
 * Documentation:	 		https://www.mediawiki.org/wiki/Extension:Wikibase_Client
 * Support					https://www.mediawiki.org/wiki/Extension_talk:Wikibase_Client
 * Source code:				https://gerrit.wikimedia.org/r/gitweb?p=mediawiki/extensions/Wikibase.git;a=tree;f=client
 *
 * @licence GNU GPL v2+
 */

/**
 * This documentation group collects source code files belonging to Wikibase Client.
 *
 * @defgroup WikibaseClient Wikibase Client
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "Not an entry point.\n" );
}

if ( defined( 'WBC_VERSION' ) ) {
	// Do not initialize more than once.
	return;
}

define( 'WBC_VERSION', '0.5 alpha'
	. ( defined( 'WB_EXPERIMENTAL_FEATURES' ) && WB_EXPERIMENTAL_FEATURES ? '/experimental' : '' ) );

if ( version_compare( $GLOBALS['wgVersion'], '1.21c', '<' ) ) { // Needs to be 1.21c because version_compare() works in confusing ways.
	die( "<b>Error:</b> Wikibase requires MediaWiki 1.21 alpha or above.\n" );
}

define( 'WBC_DIR', __DIR__ );

// Include the WikibaseLib extension if that hasn't been done yet, since it's required for WikibaseClient to work.
if ( !defined( 'WBL_VERSION' ) ) {
	include_once( __DIR__ . '/../lib/WikibaseLib.php' );
}

if ( !defined( 'WBL_VERSION' ) ) {
	throw new Exception( 'WikibaseClient depends on the WikibaseLib extension.' );
}

call_user_func( function() {
	global $wgExtensionCredits, $wgExtensionMessagesFiles, $wgHooks;
	global $wgAPIMetaModules, $wgSpecialPages, $wgSpecialPageGroups, $wgResourceModules;
	global $wgWBClientStores, $wgWBClientSettings, $wgRecentChangesFlags;


	$wgExtensionCredits['wikibase'][] = array(
		'path' => __DIR__,
		'name' => 'Wikibase Client',
		'version' => WBC_VERSION,
		'author' => array(
			'The Wikidata team', // TODO: link?
		),
		'url' => 'https://www.mediawiki.org/wiki/Extension:Wikibase_Client',
		'descriptionmsg' => 'wikibase-client-desc'
	);

	$dir = __DIR__ . '/';

	// i18n
	$wgExtensionMessagesFiles['wikibaseclient'] 		= $dir . 'WikibaseClient.i18n.php';
	$wgExtensionMessagesFiles['Wikibaseclientalias']	= $dir . 'WikibaseClient.i18n.alias.php';
	$wgExtensionMessagesFiles['wikibaseclientmagic']	= $dir . 'WikibaseClient.i18n.magic.php';

	// Hooks
	$wgHooks['UnitTestsList'][] 				= '\Wikibase\ClientHooks::registerUnitTests';
	$wgHooks['BaseTemplateToolbox'][]			= '\Wikibase\ClientHooks::onBaseTemplateToolbox';
	$wgHooks['OldChangesListRecentChangesLine'][]		= '\Wikibase\ClientHooks::onOldChangesListRecentChangesLine';
	$wgHooks['OutputPageParserOutput'][]		= '\Wikibase\ClientHooks::onOutputPageParserOutput';
	$wgHooks['ParserAfterParse'][]				= '\Wikibase\ClientHooks::onParserAfterParse';
	$wgHooks['ParserFirstCallInit'][]			= '\Wikibase\ClientHooks::onParserFirstCallInit';
	$wgHooks['MagicWordwgVariableIDs'][]			= '\Wikibase\ClientHooks::onMagicWordwgVariableIDs';
	$wgHooks['ParserGetVariableValueSwitch'][]		= '\Wikibase\ClientHooks::onParserGetVariableValueSwitch';
	$wgHooks['SkinTemplateOutputPageBeforeExec'][]		= '\Wikibase\ClientHooks::onSkinTemplateOutputPageBeforeExec';
	$wgHooks['SpecialMovepageAfterMove'][]				= '\Wikibase\ClientHooks::onSpecialMovepageAfterMove';
	$wgHooks['SpecialWatchlistQuery'][]			= '\Wikibase\ClientHooks::onSpecialWatchlistQuery';
	$wgHooks['SpecialRecentChangesQuery'][]				= '\Wikibase\ClientHooks::onSpecialRecentChangesQuery';
	$wgHooks['SpecialRecentChangesFilters'][]			= '\Wikibase\ClientHooks::onSpecialRecentChangesFilters';
	$wgHooks['GetPreferences'][]						= '\Wikibase\ClientHooks::onGetPreferences';
	$wgHooks['BeforePageDisplay'][]				= '\Wikibase\ClientHooks::onBeforePageDisplay';
	$wgHooks['BeforePageDisplay'][]             = '\Wikibase\ClientHooks::onBeforePageDisplayAddJsConfig';
	$wgHooks['ScribuntoExternalLibraries'][]      = '\Wikibase\ClientHooks::onScribuntoExternalLibraries';
	$wgHooks['SpecialWatchlistFilters'][]          = '\Wikibase\ClientHooks::onSpecialWatchlistFilters';
	$wgHooks['InfoAction'][] 								= '\Wikibase\ClientHooks::onInfoAction';
	$wgHooks['TitleMoveComplete'][]          = '\Wikibase\ClientHooks::onTitleMoveComplete';

	// extension hooks
	$wgHooks['WikibaseDeleteData'][]			= '\Wikibase\ClientHooks::onWikibaseDeleteData';
	$wgHooks['WikibaseRebuildData'][]			= '\Wikibase\ClientHooks::onWikibaseRebuildData';

	// api modules
	$wgAPIMetaModules['wikibase'] = 'Wikibase\ApiClientInfo';

	// Special page registration
	$wgSpecialPages['UnconnectedPages']						= 'Wikibase\Client\Specials\SpecialUnconnectedPages';

	// Special page groups
	$wgSpecialPageGroups['UnconnectedPages']				= 'wikibaseclient';

	// Resource loader modules
	$wgResourceModules = array_merge( $wgResourceModules, include( "$dir/resources/Resources.php" ) );

	$wgWBClientStores = array();
	$wgWBClientStores['DirectSqlStore'] = 'Wikibase\DirectSqlStore';

	$wgWBClientSettings = array_merge(
		require( __DIR__ . '/../lib/config/WikibaseLib.default.php' ),
		require( __DIR__ . '/config/WikibaseClient.default.php' )
	);

	if ( defined( 'WB_EXPERIMENTAL_FEATURES' ) && WB_EXPERIMENTAL_FEATURES ) {
		include_once( $dir . 'config/WikibaseClient.experimental.php' );
	}

	$wgRecentChangesFlags['wikibase-edit'] = array(
		'letter' => 'wikibase-rc-wikibase-edit-letter',
		'title' => 'wikibase-rc-wikibase-edit-title'
	);
} );

$wgWBSettings =& $wgWBClientSettings; // B/C alias
