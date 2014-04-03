<?php

/**
 * Aliases for the special pages of the Wikibase extension.
 *
 * @since 0.1
 *
 * @licence GNU GPL v2+
 * @author John Erling Blad < jeblad@gmail.com >
 */
// @codingStandardsIgnoreFile

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'UnconnectedPages' => array( 'UnconnectedPages', 'WithoutConnection', 'WithoutSitelinks' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'UnconnectedPages' => array( 'صفحات_غير_متصلة', 'بدون_اتصال', 'بدون_وصلات_موقع' ),
);

/** Egyptian Spoken Arabic (مصرى) */
$specialPageAliases['arz'] = array(
	'UnconnectedPages' => array( 'صفحات_مش_متوصله', 'من_غير_وصله', 'من_غير_وصلات_موقع' ),
);

/** German (Deutsch) */
$specialPageAliases['de'] = array(
	'UnconnectedPages' => array( 'Nicht_verbundene_Seiten' ),
);

/** Estonian (eesti) */
$specialPageAliases['et'] = array(
	'UnconnectedPages' => array( 'Ühendamata_leheküljed' ),
);

/** Persian (فارسی) */
$specialPageAliases['fa'] = array(
	'UnconnectedPages' => array( 'صفخات_متصل‌نشده' ),
);

/** Finnish (suomi) */
$specialPageAliases['fi'] = array(
	'UnconnectedPages' => array( 'Yhdistämättömät_sivut' ),
);

/** Hebrew (עברית) */
$specialPageAliases['he'] = array(
	'UnconnectedPages' => array( 'דפים_שלא_מקושרים_לפריטים' ),
);

/** Italian (italiano) */
$specialPageAliases['it'] = array(
	'UnconnectedPages' => array( 'PagineNonConnesse' ),
);

/** Japanese (日本語) */
$specialPageAliases['ja'] = array(
	'UnconnectedPages' => array( '関連付けられていないページ', '関連付けのないページ' ),
);

/** Korean (한국어) */
$specialPageAliases['ko'] = array(
	'UnconnectedPages' => array( '연결안된문서' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'UnconnectedPages' => array( 'Net_verbonne_Säiten', 'Ouni_Verbindung', 'Ouni_Linken_op_aner_Säiten' ),
);

/** Macedonian (македонски) */
$specialPageAliases['mk'] = array(
	'UnconnectedPages' => array( 'НесврзаниСтраници' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'UnconnectedPages' => array( 'OngekoppeldePaginas', 'OngekoppeldePagina\'s' ),
);

/** Turkish (Türkçe) */
$specialPageAliases['tr'] = array(
	'UnconnectedPages' => array( 'BağlanmamışSayfalar', 'Bağlantısız', 'SiteBağlantısız' ),
);

/** Vietnamese (Tiếng Việt) */
$specialPageAliases['vi'] = array(
	'UnconnectedPages' => array( 'Trang_không_kết_nối', 'Trang_không_có_liên_kết_site' ),
);

/** Simplified Chinese (中文（简体）‎) */
$specialPageAliases['zh-hans'] = array(
	'UnconnectedPages' => array( '未连接页面', '无连接页面', '无站点连接' ),
);

/** Traditional Chinese (中文（繁體）‎) */
$specialPageAliases['zh-hant'] = array(
	'UnconnectedPages' => array( '無連接頁面', '失去連接', '失去站點連接' ),
);