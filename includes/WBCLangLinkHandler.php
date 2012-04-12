<?php

/**
 * Handles language links.
 * TODO: do we really want to refresh this on re-render? push updates from the repo seem to make more sense
 * TODO: Should we sort the links if we don't display them? Several cases: links disabled per namespace, links disabled by parser function/magic word, no links on the server.
 *
 * @since 0.1
 *
 * @file WBCLangLinkHandler.php
 * @ingroup WikibaseClient
 *
 * @licence	GNU GPL v2+
 * @author	Nikola Smolenski <smolensk@eunet.rs>
 */
class WBCLangLinkHandler {

	protected static $cache = array();
	protected static $sort_order = false;

	public static function onParserBeforeTidy( Parser &$parser, &$text ) {
		global $wgLanguageCode;

		$title = $parser->getTitle();

		//TODO: alwaysSort
		if( $parser->getOptions()->getInterfaceMessage() || !in_array( $title->getNamespace(), WBCSettings::get( 'namespaces' ) ) ) {
			return true;
		}

		$db_title = $title->getDBkey();
		if( isset( self::$cache[$db_title] ) ) {
			$links = self::$cache[$db_title];
		} else {
			$links = self::getLinks( $db_title );

			// If there was an error while getting links, we use the current links.
			if( $links === false ) {
				$links = self::readLinksFromDB( wfGetDB( DB_SLAVE ), $title->getArticleID() );
			}

			self::$cache[$db_title] = $links;
		}

		// If a link exists in wikitext, override wikidata link to the same language.
		$out = $parser->getOutput();

		$nei = self::getNoExternalInterlang( $out );
		if( array_key_exists( '*', $nei ) ) {
			$links = array();
		} else {
			// Always remove the link to the site language.
			unset( $links[$wgLanguageCode] );

			$links = array_diff_key( $links, $nei );
		}

		// Pack the links properly into mLanguageLinks.
		foreach( $links as $lang => $link ) {
			$out->addLanguageLink( "$lang:$link" );
		}

		//Sort the links
		self::sortLinks( $out->mLanguageLinks );

		return true;
	}

	/**
	 * Get no_external_interlang parser property.
	 *
	 * @return Array Empty array if not set.
	 */
	public static function getNoExternalInterlang( ParserOutput $out ) {
		$nei = $out->getProperty( 'no_external_interlang' );

		if( empty( $nei ) ) {
			$nei = array();
		} else {
			$nei = unserialize( $nei );
		}

		return $nei;
	}

	/**
	 * Get language code from a link in ParserOutput::mLanguageLinks
	 */
	protected static function getCodeFromLink( $link ) {
		return substr( $link, 0, strpos( $link, ':' ) );
	}

	/**
	 * Get the list of links for a title.
	 * @return Array of links, empty array for no links, false for failure.
	 */
	protected static function getLinks( $db_title ) {
		$dir = dirname( __FILE__ ) . '/';
		$file = "$dir/../test/$db_title.json";
		if( file_exists( $file ) ) {
			return get_object_vars( json_decode( file_get_contents( $file ) ) );
		} else {
			return false;
		}
	}

	/**
	 * Read interlanguage links from a database, and return them in the same format as getLinks()
	 *
	 * @param	$dbr - Database.
	 * @param	$articleid - ID of the article whose links should be returned.
	 * @returns	The array with the links. If there are no links, an empty array is returned.
	 * @version	Copied from InterlanguageExtension rev 114818
	 */
	protected static function readLinksFromDB( $dbr, $articleid ) {
		$res = $dbr->select(
			array( 'langlinks' ),
			array( 'll_lang', 'll_title' ),
			array( 'll_from' => $articleid ),
			__FUNCTION__
		);
		$a = array();
		foreach( $res as $row ) {
			$a[$row->ll_lang] = $row->ll_title;
		}
		return $a;
	}

	/**
	 * Sort an array of links in-place
	 * @version	Copied from InterlanguageExtension rev 114818
	 */
	protected static function sortLinks( &$a ) {
		// http://meta.wikimedia.org/w/index.php?title=Interwiki_sorting_order&oldid=2022604#By_order_of_alphabet.2C_based_on_local_language
		static $order_alphabetic = array(
			'ace', 'af', 'ak', 'als', 'am', 'ang', 'ab', 'ar', 'an', 'arc',
			'roa-rup', 'frp', 'as', 'ast', 'gn', 'av', 'ay', 'az', 'bm', 'bn',
			'zh-min-nan', 'nan', 'map-bms', 'ba', 'be', 'be-x-old', 'bh', 'bcl',
			'bi', 'bar', 'bo', 'bs', 'br', 'bg', 'bxr', 'ca', 'cv', 'ceb', 'cs',
			'ch', 'cbk-zam', 'ny', 'sn', 'tum', 'cho', 'co', 'cy', 'da', 'dk',
			'pdc', 'de', 'dv', 'nv', 'dsb', 'dz', 'mh', 'et', 'el', 'eml', 'en',
			'myv', 'es', 'eo', 'ext', 'eu', 'ee', 'fa', 'hif', 'fo', 'fr', 'fy',
			'ff', 'fur', 'ga', 'gv', 'gd', 'gl', 'gan', 'ki', 'glk', 'gu',
			'got', 'hak', 'xal', 'ko', 'ha', 'haw', 'hy', 'hi', 'ho', 'hsb',
			'hr', 'io', 'ig', 'ilo', 'bpy', 'id', 'ia', 'ie', 'iu', 'ik', 'os',
			'xh', 'zu', 'is', 'it', 'he', 'jv', 'kl', 'kn', 'kr', 'pam', 'krc',
			'ka', 'ks', 'csb', 'kk', 'kw', 'rw', 'ky', 'rn', 'sw', 'kv', 'kg',
			'ht', 'ku', 'kj', 'lad', 'lbe', 'lo', 'la', 'lv', 'lb', 'lt', 'lij',
			'li', 'ln', 'jbo', 'lg', 'lmo', 'hu', 'mk', 'mg', 'ml', 'mt', 'mi',
			'mr', 'arz', 'mzn', 'ms', 'cdo', 'mwl', 'mdf', 'mo', 'mn', 'mus',
			'my', 'nah', 'na', 'fj', 'nl', 'nds-nl', 'cr', 'ne', 'new', 'ja',
			'nap', 'ce', 'pih', 'no', 'nb', 'nn', 'nrm', 'nov', 'ii', 'oc',
			'mhr', 'or', 'om', 'ng', 'hz', 'uz', 'pa', 'pi', 'pag', 'pnb',
			'pap', 'ps', 'km', 'pcd', 'pms', 'tpi', 'nds', 'pl', 'tokipona',
			'tp', 'pnt', 'pt', 'aa', 'kaa', 'crh', 'ty', 'ksh', 'ro', 'rmy',
			'rm', 'qu', 'ru', 'sah', 'se', 'sm', 'sa', 'sg', 'sc', 'sco', 'stq',
			'st', 'tn', 'sq', 'scn', 'si', 'simple', 'sd', 'ss', 'sk', 'cu',
			'sl', 'szl', 'so', 'ckb', 'srn', 'sr', 'sh', 'su', 'fi', 'sv', 'tl',
			'ta', 'kab', 'roa-tara', 'tt', 'te', 'tet', 'th', 'ti', 'tg', 'to',
			'chr', 'chy', 've', 'tr', 'tk', 'tw', 'udm', 'bug', 'uk', 'ur',
			'ug', 'za', 'vec', 'vi', 'vo', 'fiu-vro', 'wa', 'zh-classical',
			'vls', 'war', 'wo', 'wuu', 'ts', 'yi', 'yo', 'zh-yue', 'diq', 'zea',
			'bat-smg', 'zh', 'zh-tw', 'zh-cn',
		);

		// http://meta.wikimedia.org/w/index.php?title=Interwiki_sorting_order&oldid=2022604#By_order_of_alphabet.2C_based_on_local_language_.28by_first_word.29
		static $order_alphabetic_revised = array(
			'ace', 'af', 'ak', 'als', 'am', 'ang', 'ab', 'ar', 'an', 'arc',
			'roa-rup', 'frp', 'as', 'ast', 'gn', 'av', 'ay', 'az', 'id', 'ms',
			'bm', 'bn', 'zh-min-nan', 'nan', 'map-bms', 'jv', 'su', 'ba', 'be',
			'be-x-old', 'bh', 'bcl', 'bi', 'bar', 'bo', 'bs', 'br', 'bug', 'bg',
			'bxr', 'ca', 'ceb', 'cv', 'cs', 'ch', 'cbk-zam', 'ny', 'sn', 'tum',
			'cho', 'co', 'cy', 'da', 'dk', 'pdc', 'de', 'dv', 'nv', 'dsb', 'na',
			'dz', 'mh', 'et', 'el', 'eml', 'en', 'myv', 'es', 'eo', 'ext', 'eu',
			'ee', 'fa', 'hif', 'fo', 'fr', 'fy', 'ff', 'fur', 'ga', 'gv', 'sm',
			'gd', 'gl', 'gan', 'ki', 'glk', 'gu', 'got', 'hak', 'xal', 'ko',
			'ha', 'haw', 'hy', 'hi', 'ho', 'hsb', 'hr', 'io', 'ig', 'ilo',
			'bpy', 'ia', 'ie', 'iu', 'ik', 'os', 'xh', 'zu', 'is', 'it', 'he',
			'kl', 'kn', 'kr', 'pam', 'ka', 'ks', 'csb', 'kk', 'kw', 'rw', 'ky',
			'rn', 'sw', 'kv', 'kg', 'ht', 'ku', 'kj', 'lad', 'lbe', 'lo', 'la',
			'lv', 'to', 'lb', 'lt', 'lij', 'li', 'ln', 'jbo', 'lg', 'lmo', 'hu',
			'mk', 'mg', 'ml', 'krc', 'mt', 'mi', 'mr', 'arz', 'mzn', 'cdo',
			'mwl', 'mdf', 'mo', 'mn', 'mus', 'my', 'nah', 'fj', 'nl', 'nds-nl',
			'cr', 'ne', 'new', 'ja', 'nap', 'ce', 'pih', 'no', 'nb', 'nn',
			'nrm', 'nov', 'ii', 'oc', 'mhr', 'or', 'om', 'ng', 'hz', 'uz', 'pa',
			'pi', 'pag', 'pnb', 'pap', 'ps', 'km', 'pcd', 'pms', 'nds', 'pl',
			'pnt', 'pt', 'aa', 'kaa', 'crh', 'ty', 'ksh', 'ro', 'rmy', 'rm',
			'qu', 'ru', 'sah', 'se', 'sa', 'sg', 'sc', 'sco', 'stq', 'st', 'tn',
			'sq', 'scn', 'si', 'simple', 'sd', 'ss', 'sk', 'sl', 'cu', 'szl',
			'so', 'ckb', 'srn', 'sr', 'sh', 'fi', 'sv', 'tl', 'ta', 'kab',
			'roa-tara', 'tt', 'te', 'tet', 'th', 'vi', 'ti', 'tg', 'tpi',
			'tokipona', 'tp', 'chr', 'chy', 've', 'tr', 'tk', 'tw', 'udm', 'uk',
			'ur', 'ug', 'za', 'vec', 'vo', 'fiu-vro', 'wa', 'zh-classical',
			'vls', 'war', 'wo', 'wuu', 'ts', 'yi', 'yo', 'zh-yue', 'diq', 'zea',
			'bat-smg', 'zh', 'zh-tw', 'zh-cn',
		);

		$sort = WBCSettings::get( 'sort' );

		// Prepare the sorting array.
		if( self::$sort_order === false ) {
			switch( $sort ) {
				case 'code':
					self::$sort_order = $order_alphabetic;
					sort( self::$sort_order );
					break;
				case 'alphabetic':
					self::$sort_order = $order_alphabetic;
					break;
				case 'alphabetic_revised':
					self::$sort_order = $order_alphabetic_revised;
					break;
				case 'none':
				default:
					// If we encounter an unknown sort setting, just do nothing, for we are kind and generous.
					return;
					break;
			}

			$sortPrepend = WBCSettings::get( 'sortPrepend' );
			if( is_array( $sortPrepend ) ) {
				self::$sort_order = array_unique( array_merge( $sortPrepend, self::$sort_order ) );
			}
			self::$sort_order = array_flip( self::$sort_order );

		}

		usort( $a, 'WBCLangLinkHandler::compareLinks' );
	}

	/**
	 * usort() callback function, compares the links on the basis of self::$sort_order
	 */
	protected static function compareLinks( $a, $b ) {
		$a = self::getCodeFromLink( $a );
		$b = self::getCodeFromLink( $b );

		if( $a == $b ) return 0;

		// If we encounter an unknown language, which may happen if the sort table is not updated, we move it to the bottom.
		$a = self::$sort_order[$a];
		if( $a === null ) $a = 999999;
		$b = self::$sort_order[$b];
		if( $b === null ) $b = 999999;

		return ( $a > $b )? 1: ( ( $a < $b )? -1: 0 );
	}

}
