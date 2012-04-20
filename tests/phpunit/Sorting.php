<?php

/**
 * Tests for the WikibaseItem class.
 *
 * @file
 * @since 0.1
 *
 * @ingroup Wikibase
 * @ingroup Test
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class WikibaseSortingTests extends MediaWikiTestCase {

	/**
	 * No sorting.
	 */
	public function testNone() {
		global $egWBCSettings;
		unset($egWBCSettings['sort']);

		$sorted = $orig = $this->provideArray();
		WBCLangLinkHandler::sortLinks( $sorted );
		$this->assertEquals( $sorted, $orig );
	}

	/**
	 * Sort by language code.
	 */
	public function testCode() {
		global $egWBCSettings;
		$egWBCSettings['sort'] = 'code';

		$sorted = $this->provideArray();
		WBCLangLinkHandler::sortLinks( $sorted );
		$this->assertEquals( $sorted, array(
			"ace:Wikipèdia",
			"af:Wikipedia",
			"ak:Wikipedia",
			"als:Wikipedia",
			"am:ውክፔዲያ",
			"an:Biquipedia",
			"ang:Ƿikipǣdia",
			"arc:ܘܝܩܝܦܕܝܐ",
			"arz:ويكيبيديا",
			"ar:ويكيبيديا",
			"as:ৱিকিপিডিয়া",
			"ast:Uiquipedia",
			"av:Википедия",
			"ay:Wikipidiya",
			"az:Vikipediya",
			"bar:Wikipedia",
			"bat-smg:Vikipedėjė",
			"ba:Википедия",
			"bcl:Wikipidya",
			"be-x-old:Вікіпэдыя",
			"be:Вікіпедыя",
			"bg:Уикипедия",
			"bh:विकिपीडिया",
			"bi:Wikipedia",
			"bjn:Wikipidia",
			"bm:Wikipedi",
			"bn:উইকিপিডিয়া",
			"bpy:উইকিপিডিয়া",
			"br:Wikipedia",
			"bs:Wikipedia",
			"bug:Wikipedia",
			"bxr:Википеэди",
			"ca:Viquipèdia",
			"cbk-zam:Wikipedia",
			"cdo:Wikipedia",
			"ceb:Wikipedya",
			"ce:Википедийа",
			"chr:ᏫᎩᏇᏗᏯ",
			"ch:Wikipedia",
			"chy:Wikipedia",
			"ckb:ویکیپیدیا",
			"co:Wikipedia",
			"crh:Vikipediya",
			"csb:Wikipedijô",
			"cs:Wikipedie",
			"cu:Википєдїꙗ",
			"cv:Википеди",
			"cy:Wicipedia",
			"da:Wikipedia",
			"de:Wikipedia",
			"diq:Wikipediya",
			"dsb:Wikipedija",
			"dv:ވިކިޕީޑިޔާ",
			"el:Βικιπαίδεια",
			"eml:Vichipedìa",
			"eo:Vikipedio",
			"es:Wikipedia",
			"et:Vikipeedia",
			"eu:Wikipedia",
			"ext:Wikipédia",
			"fa:ویکی‌پدیا",
			"ff:Wikipeediya",
			"fiu-vro:Vikipeediä",
			"fi:Wikipedia",
			"fo:Wikipedia",
			"frp:Vouiquipèdia",
			"frr:Wikipedia",
			"fr:Wikipédia",
			"fur:Vichipedie",
			"fy:Wikipedia",
			"gag:Vikipediya",
			"gan:維基百科",
			"ga:Vicipéid",
			"gd:Uicipeid",
			"glk:ویکیپدیا جی",
			"gl:Wikipedia",
			"gn:Vikipetã",
			"got:𐍅𐌹𐌺𐌹𐍀𐌴𐌳𐌾𐌰",
			"gu:વિકિપીડિયા",
			"gv:Wikipedia",
			"hak:Ngùi-kî pak-khô",
			"haw:Wikipikia",
			"he:ויקיפדיה",
			"hi:विकिपीडिया",
			"hr:Wikipedija",
			"hsb:Wikipedija",
			"ht:Wikipedya",
			"hu:Wikipédia",
			"hy:Վիքիպեդիա",
			"ia:Wikipedia",
			"id:Wikipedia",
			"ie:Wikipedia",
			"ig:Wikipedia",
			"ik:Uiqipitia",
			"ilo:Wikipedia",
			"io:Wikipedio",
			"is:Wikipedia",
			"it:Wikipedia",
			"iu:ᐅᐃᑭᐱᑎᐊ",
			"ja:ウィキペディア",
			"jbo:uikipidias",
			"jv:Wikipedia",
			"kaa:Wikipedia",
			"kab:Wikipédia",
			"ka:ვიკიპედია",
			"kk:Уикипедия",
			"kl:Wikipedia",
			"km:វិគីភីឌា",
			"kn:ವಿಕಿಪೀಡಿಯ",
			"ko:위키백과",
			"koi:Википедия",
			"krc:Википедия",
			"ksh:Wikkipedija",
			"ks:ویکیپیٖڈیا",
			"ku:Wîkîpediya",
			"kw:Wikipedya",
			"ky:Уикипедия",
			"lad:Vikipedya",
			"la:Vicipaedia",
			"lbe:Википедия",
			"lb:Wikipedia",
			"lez:Википедия",
			"lij:Wikipedia",
			"li:Wikipedia",
			"lmo:Wikipedia",
			"ln:Wikipedia",
			"lo:ວິກິພີເດຍ",
			"ltg:Vikipedeja",
			"lt:Vikipedija",
			"lv:Vikipēdija",
			"map-bms:Wikipedia",
			"mdf:Википедие",
			"mg:Wikipedia",
			"mhr:Википедий",
			"mi:Wikipedia",
			"mk:Википедија",
			"ml:വിക്കിപീഡിയ",
			"mn:Википедиа",
			"mr:विकिपीडिया",
			"mrj:Википеди",
			"ms:Wikipedia",
			"mt:Wikipedija",
			"mwl:Biquipédia",
			"my:ဝီကီပီးဒီးယား",
			"myv:Википедиясь",
			"mzn:ویکی‌پدیا",
			"nah:Huiquipedia",
			"nap:Wikipedia",
			"na:Wikipediya",
			"nds-nl:Wikipedie",
			"nds:Wikipedia",
			"ne:विकिपीडिया",
			"new:विकिपिडिया",
			"nl:Wikipedia",
			"nn:Wikipedia",
			"nov:Wikipedie",
			"no:Wikipedia",
			"nrm:Viqùipédie",
			"nso:Wikipedia",
			"nv:Íiyisíí Naaltsoos",
			"oc:Wikipèdia",
			"or:ଉଇକିପିଡ଼ିଆ",
			"os:Википеди",
			"pa:ਵਿਕਿਪੀਡਿਆ",
			"pag:Wikipedia",
			"pam:Wikipedia",
			"pap:Wikipedia",
			"pcd:Wikipédia",
			"pdc:Wikipedia",
			"pfl:Wikipedia",
			"pih:Wikkapedya",
			"pl:Wikipedia",
			"pms:Wikipedia",
			"pnb:وکیپیڈیا",
			"pnt:Βικιπαίδεια",
			"ps:ويکيپېډيا",
			"pt:Wikipédia",
			"qu:Wikipidiya",
			"rm:Vichipedia",
			"rmy:Vikipidiya",
			"roa-rup:Wikipedia",
			"roa-tara:Uicchipèdie",
			"ro:Wikipedia",
			"rue:Вікіпедія",
			"ru:Википедия",
			"sa:विकिपीडिया",
			"sah:Бикипиэдьийэ",
			"scn:Wikipedia",
			"sco:Wikipaedia",
			"sc:Wikipedia",
			"sd:وڪيپيڊيا",
			"se:Wikipedia",
			"sg:Wïkïpêdïyäa",
			"sh:Wikipedia",
			"si:විකිපීඩියා",
			"simple:Wikipedia",
			"sk:Wikipédia",
			"sl:Wikipedija",
			"sm:Wikipedia",
			"so:Wikipedia",
			"sq:Wikipedia",
			"srn:Wikipedia",
			"sr:Википедија",
			"ss:Wikipedia",
			"stq:Wikipedia",
			"su:Wikipédia",
			"sv:Wikipedia",
			"sw:Wikipedia",
			"szl:Wikipedyjo",
			"ta:விக்கிப்பீடியா",
			"te:వికీపీడియా",
			"tet:Wikipédia",
			"tg:Википедиа",
			"th:วิกิพีเดีย",
			"ti:ዊኪፔዲያ",
			"tk:Wikipediýa",
			"tl:Wikipedia",
			"tn:Wikipedia",
			"tpi:Wikipedia",
			"tr:Vikipedi",
			"ts:Wikipediya",
			"tt:Wikipedia",
			"tw:Wikipedia",
			"ty:Vitipetia",
			"udm:Википедия",
			"ug:ۋىكىپېدىيە",
			"uk:Вікіпедія",
			"ur:ویکیپیڈیا",
			"uz:Vikipediya",
			"vec:Wikipedia",
			"vep:Vikipedii",
			"ve:Wikipedia",
			"vi:Wikipedia",
			"vls:Wikipedia",
			"vo:Vükiped",
			"war:Wikipedia",
			"wa:Wikipedia",
			"wo:Wikipedia",
			"wuu:维基百科",
			"xal:Бикипеди",
			"xmf:ვიკიპედია",
			"yi:וויקיפעדיע",
			"yo:Wikipedia",
			"za:Vigibakgoh",
			"zea:Wikipedia",
			"zh-classical:維基大典",
			"zh-min-nan:Wikipedia",
			"zh-yue:維基百科",
			"zh:维基百科",
			"zu:Wikipedia",
		) );
	}

	public function testAlphabetic() {
		global $egWBCSettings;
		$egWBCSettings['sort'] = 'alphabetic';

		$sorted = $this->provideArray();
		WBCLangLinkHandler::sortLinks( $sorted );
		$this->assertEquals( $sorted, array(
			"ace:Wikipèdia",
			"af:Wikipedia",
			"ak:Wikipedia",
			"als:Wikipedia",
			"am:ውክፔዲያ",
			"ang:Ƿikipǣdia",
			"ar:ويكيبيديا",
			"an:Biquipedia",
			"arc:ܘܝܩܝܦܕܝܐ",
			"roa-rup:Wikipedia",
			"frp:Vouiquipèdia",
			"as:ৱিকিপিডিয়া",
			"ast:Uiquipedia",
			"gn:Vikipetã",
			"av:Википедия",
			"ay:Wikipidiya",
			"az:Vikipediya",
			"bm:Wikipedi",
			"bn:উইকিপিডিয়া",
			"bjn:Wikipidia",
			"zh-min-nan:Wikipedia",
			"map-bms:Wikipedia",
			"ba:Википедия",
			"be:Вікіпедыя",
			"be-x-old:Вікіпэдыя",
			"bh:विकिपीडिया",
			"bcl:Wikipidya",
			"bi:Wikipedia",
			"bg:Уикипедия",
			"bar:Wikipedia",
			"bs:Wikipedia",
			"br:Wikipedia",
			"bxr:Википеэди",
			"ca:Viquipèdia",
			"cv:Википеди",
			"ceb:Wikipedya",
			"cs:Wikipedie",
			"ch:Wikipedia",
			"cbk-zam:Wikipedia",
			"co:Wikipedia",
			"cy:Wicipedia",
			"da:Wikipedia",
			"pdc:Wikipedia",
			"de:Wikipedia",
			"dv:ވިކިޕީޑިޔާ",
			"nv:Íiyisíí Naaltsoos",
			"dsb:Wikipedija",
			"et:Vikipeedia",
			"el:Βικιπαίδεια",
			"eml:Vichipedìa",
			"myv:Википедиясь",
			"es:Wikipedia",
			"eo:Vikipedio",
			"ext:Wikipédia",
			"eu:Wikipedia",
			"fa:ویکی‌پدیا",
			"fo:Wikipedia",
			"fr:Wikipédia",
			"fy:Wikipedia",
			"ff:Wikipeediya",
			"fur:Vichipedie",
			"ga:Vicipéid",
			"gv:Wikipedia",
			"gag:Vikipediya",
			"gd:Uicipeid",
			"gl:Wikipedia",
			"gan:維基百科",
			"glk:ویکیپدیا جی",
			"gu:વિકિપીડિયા",
			"got:𐍅𐌹𐌺𐌹𐍀𐌴𐌳𐌾𐌰",
			"hak:Ngùi-kî pak-khô",
			"xal:Бикипеди",
			"ko:위키백과",
			"haw:Wikipikia",
			"hy:Վիքիպեդիա",
			"hi:विकिपीडिया",
			"hsb:Wikipedija",
			"hr:Wikipedija",
			"io:Wikipedio",
			"ig:Wikipedia",
			"ilo:Wikipedia",
			"bpy:উইকিপিডিয়া",
			"id:Wikipedia",
			"ia:Wikipedia",
			"ie:Wikipedia",
			"iu:ᐅᐃᑭᐱᑎᐊ",
			"ik:Uiqipitia",
			"os:Википеди",
			"zu:Wikipedia",
			"is:Wikipedia",
			"it:Wikipedia",
			"he:ויקיפדיה",
			"jv:Wikipedia",
			"kl:Wikipedia",
			"kn:ವಿಕಿಪೀಡಿಯ",
			"pam:Wikipedia",
			"krc:Википедия",
			"ka:ვიკიპედია",
			"ks:ویکیپیٖڈیا",
			"csb:Wikipedijô",
			"kk:Уикипедия",
			"kw:Wikipedya",
			"sw:Wikipedia",
			"ht:Wikipedya",
			"ku:Wîkîpediya",
			"ky:Уикипедия",
			"mrj:Википеди",
			"lad:Vikipedya",
			"lbe:Википедия",
			"lez:Википедия",
			"lo:ວິກິພີເດຍ",
			"ltg:Vikipedeja",
			"la:Vicipaedia",
			"lv:Vikipēdija",
			"lb:Wikipedia",
			"lt:Vikipedija",
			"lij:Wikipedia",
			"li:Wikipedia",
			"ln:Wikipedia",
			"jbo:uikipidias",
			"lmo:Wikipedia",
			"hu:Wikipédia",
			"mk:Википедија",
			"mg:Wikipedia",
			"ml:വിക്കിപീഡിയ",
			"mt:Wikipedija",
			"mi:Wikipedia",
			"mr:विकिपीडिया",
			"xmf:ვიკიპედია",
			"arz:ويكيبيديا",
			"mzn:ویکی‌پدیا",
			"ms:Wikipedia",
			"cdo:Wikipedia",
			"mwl:Biquipédia",
			"mdf:Википедие",
			"mn:Википедиа",
			"my:ဝီကီပီးဒီးယား",
			"nah:Huiquipedia",
			"na:Wikipediya",
			"nl:Wikipedia",
			"nds-nl:Wikipedie",
			"ne:विकिपीडिया",
			"new:विकिपिडिया",
			"ja:ウィキペディア",
			"nap:Wikipedia",
			"ce:Википедийа",
			"frr:Wikipedia",
			"pih:Wikkapedya",
			"no:Wikipedia",
			"nn:Wikipedia",
			"nrm:Viqùipédie",
			"nov:Wikipedie",
			"oc:Wikipèdia",
			"mhr:Википедий",
			"or:ଉଇକିପିଡ଼ିଆ",
			"uz:Vikipediya",
			"pa:ਵਿਕਿਪੀਡਿਆ",
			"pfl:Wikipedia",
			"pag:Wikipedia",
			"pnb:وکیپیڈیا",
			"pap:Wikipedia",
			"ps:ويکيپېډيا",
			"koi:Википедия",
			"km:វិគីភីឌា",
			"pcd:Wikipédia",
			"pms:Wikipedia",
			"tpi:Wikipedia",
			"nds:Wikipedia",
			"pl:Wikipedia",
			"pnt:Βικιπαίδεια",
			"pt:Wikipédia",
			"kaa:Wikipedia",
			"crh:Vikipediya",
			"ty:Vitipetia",
			"ksh:Wikkipedija",
			"ro:Wikipedia",
			"rmy:Vikipidiya",
			"rm:Vichipedia",
			"qu:Wikipidiya",
			"rue:Вікіпедія",
			"ru:Википедия",
			"sah:Бикипиэдьийэ",
			"se:Wikipedia",
			"sm:Wikipedia",
			"sa:विकिपीडिया",
			"sg:Wïkïpêdïyäa",
			"sc:Wikipedia",
			"sco:Wikipaedia",
			"stq:Wikipedia",
			"nso:Wikipedia",
			"tn:Wikipedia",
			"sq:Wikipedia",
			"scn:Wikipedia",
			"si:විකිපීඩියා",
			"simple:Wikipedia",
			"sd:وڪيپيڊيا",
			"ss:Wikipedia",
			"sk:Wikipédia",
			"sl:Wikipedija",
			"cu:Википєдїꙗ",
			"szl:Wikipedyjo",
			"so:Wikipedia",
			"ckb:ویکیپیدیا",
			"srn:Wikipedia",
			"sr:Википедија",
			"sh:Wikipedia",
			"su:Wikipédia",
			"fi:Wikipedia",
			"sv:Wikipedia",
			"tl:Wikipedia",
			"ta:விக்கிப்பீடியா",
			"kab:Wikipédia",
			"roa-tara:Uicchipèdie",
			"tt:Wikipedia",
			"te:వికీపీడియా",
			"tet:Wikipédia",
			"th:วิกิพีเดีย",
			"ti:ዊኪፔዲያ",
			"tg:Википедиа",
			"chr:ᏫᎩᏇᏗᏯ",
			"chy:Wikipedia",
			"ve:Wikipedia",
			"tr:Vikipedi",
			"tk:Wikipediýa",
			"tw:Wikipedia",
			"udm:Википедия",
			"bug:Wikipedia",
			"uk:Вікіпедія",
			"ur:ویکیپیڈیا",
			"ug:ۋىكىپېدىيە",
			"za:Vigibakgoh",
			"vec:Wikipedia",
			"vep:Vikipedii",
			"vi:Wikipedia",
			"vo:Vükiped",
			"fiu-vro:Vikipeediä",
			"wa:Wikipedia",
			"zh-classical:維基大典",
			"vls:Wikipedia",
			"war:Wikipedia",
			"wo:Wikipedia",
			"wuu:维基百科",
			"ts:Wikipediya",
			"yi:וויקיפעדיע",
			"yo:Wikipedia",
			"zh-yue:維基百科",
			"diq:Wikipediya",
			"zea:Wikipedia",
			"bat-smg:Vikipedėjė",
			"zh:维基百科",
		) );
	}

	public function testAlphabeticRevised() {
		global $egWBCSettings;
		$egWBCSettings['sort'] = 'alphabetic_revised';

		$sorted = $this->provideArray();
		WBCLangLinkHandler::sortLinks( $sorted );
		$this->assertEquals( $sorted, array(
			"ace:Wikipèdia",
			"af:Wikipedia",
			"ak:Wikipedia",
			"als:Wikipedia",
			"am:ውክፔዲያ",
			"ang:Ƿikipǣdia",
			"ar:ويكيبيديا",
			"an:Biquipedia",
			"arc:ܘܝܩܝܦܕܝܐ",
			"roa-rup:Wikipedia",
			"frp:Vouiquipèdia",
			"as:ৱিকিপিডিয়া",
			"ast:Uiquipedia",
			"gn:Vikipetã",
			"av:Википедия",
			"ay:Wikipidiya",
			"az:Vikipediya",
			"bjn:Wikipidia",
			"id:Wikipedia",
			"ms:Wikipedia",
			"bm:Wikipedi",
			"bn:উইকিপিডিয়া",
			"zh-min-nan:Wikipedia",
			"map-bms:Wikipedia",
			"jv:Wikipedia",
			"su:Wikipédia",
			"ba:Википедия",
			"be:Вікіпедыя",
			"be-x-old:Вікіпэдыя",
			"bh:विकिपीडिया",
			"bcl:Wikipidya",
			"bi:Wikipedia",
			"bar:Wikipedia",
			"bs:Wikipedia",
			"br:Wikipedia",
			"bug:Wikipedia",
			"bg:Уикипедия",
			"bxr:Википеэди",
			"ca:Viquipèdia",
			"ceb:Wikipedya",
			"cv:Википеди",
			"cs:Wikipedie",
			"ch:Wikipedia",
			"cbk-zam:Wikipedia",
			"co:Wikipedia",
			"cy:Wicipedia",
			"da:Wikipedia",
			"pdc:Wikipedia",
			"de:Wikipedia",
			"dv:ވިކިޕީޑިޔާ",
			"nv:Íiyisíí Naaltsoos",
			"dsb:Wikipedija",
			"na:Wikipediya",
			"et:Vikipeedia",
			"el:Βικιπαίδεια",
			"eml:Vichipedìa",
			"myv:Википедиясь",
			"es:Wikipedia",
			"eo:Vikipedio",
			"ext:Wikipédia",
			"eu:Wikipedia",
			"fa:ویکی‌پدیا",
			"fo:Wikipedia",
			"fr:Wikipédia",
			"fy:Wikipedia",
			"ff:Wikipeediya",
			"fur:Vichipedie",
			"ga:Vicipéid",
			"gv:Wikipedia",
			"sm:Wikipedia",
			"gag:Vikipediya",
			"gd:Uicipeid",
			"gl:Wikipedia",
			"gan:維基百科",
			"glk:ویکیپدیا جی",
			"gu:વિકિપીડિયા",
			"got:𐍅𐌹𐌺𐌹𐍀𐌴𐌳𐌾𐌰",
			"hak:Ngùi-kî pak-khô",
			"xal:Бикипеди",
			"ko:위키백과",
			"haw:Wikipikia",
			"hy:Վիքիպեդիա",
			"hi:विकिपीडिया",
			"hsb:Wikipedija",
			"hr:Wikipedija",
			"io:Wikipedio",
			"ig:Wikipedia",
			"ilo:Wikipedia",
			"bpy:উইকিপিডিয়া",
			"ia:Wikipedia",
			"ie:Wikipedia",
			"iu:ᐅᐃᑭᐱᑎᐊ",
			"ik:Uiqipitia",
			"os:Википеди",
			"zu:Wikipedia",
			"is:Wikipedia",
			"it:Wikipedia",
			"he:ויקיפדיה",
			"kl:Wikipedia",
			"kn:ವಿಕಿಪೀಡಿಯ",
			"pam:Wikipedia",
			"ka:ვიკიპედია",
			"ks:ویکیپیٖڈیا",
			"csb:Wikipedijô",
			"kk:Уикипедия",
			"kw:Wikipedya",
			"ky:Уикипедия",
			"mrj:Википеди",
			"sw:Wikipedia",
			"ht:Wikipedya",
			"ku:Wîkîpediya",
			"lad:Vikipedya",
			"lbe:Википедия",
			"lez:Википедия",
			"lo:ວິກິພີເດຍ",
			"la:Vicipaedia",
			"ltg:Vikipedeja",
			"lv:Vikipēdija",
			"lb:Wikipedia",
			"lt:Vikipedija",
			"lij:Wikipedia",
			"li:Wikipedia",
			"ln:Wikipedia",
			"jbo:uikipidias",
			"lmo:Wikipedia",
			"hu:Wikipédia",
			"mk:Википедија",
			"mg:Wikipedia",
			"ml:വിക്കിപീഡിയ",
			"krc:Википедия",
			"mt:Wikipedija",
			"mi:Wikipedia",
			"mr:विकिपीडिया",
			"xmf:ვიკიპედია",
			"arz:ويكيبيديا",
			"mzn:ویکی‌پدیا",
			"cdo:Wikipedia",
			"mwl:Biquipédia",
			"koi:Википедия",
			"mdf:Википедие",
			"mn:Википедиа",
			"my:ဝီကီပီးဒီးယား",
			"nah:Huiquipedia",
			"nl:Wikipedia",
			"nds-nl:Wikipedie",
			"ne:विकिपीडिया",
			"new:विकिपिडिया",
			"ja:ウィキペディア",
			"nap:Wikipedia",
			"ce:Википедийа",
			"frr:Wikipedia",
			"pih:Wikkapedya",
			"no:Wikipedia",
			"nn:Wikipedia",
			"nrm:Viqùipédie",
			"nov:Wikipedie",
			"oc:Wikipèdia",
			"mhr:Википедий",
			"or:ଉଇକିପିଡ଼ିଆ",
			"uz:Vikipediya",
			"pa:ਵਿਕਿਪੀਡਿਆ",
			"pfl:Wikipedia",
			"pag:Wikipedia",
			"pnb:وکیپیڈیا",
			"pap:Wikipedia",
			"ps:ويکيپېډيا",
			"km:វិគីភីឌា",
			"pcd:Wikipédia",
			"pms:Wikipedia",
			"nds:Wikipedia",
			"pl:Wikipedia",
			"pnt:Βικιπαίδεια",
			"pt:Wikipédia",
			"kaa:Wikipedia",
			"crh:Vikipediya",
			"ty:Vitipetia",
			"ksh:Wikkipedija",
			"ro:Wikipedia",
			"rmy:Vikipidiya",
			"rm:Vichipedia",
			"qu:Wikipidiya",
			"ru:Википедия",
			"rue:Вікіпедія",
			"sah:Бикипиэдьийэ",
			"se:Wikipedia",
			"sa:विकिपीडिया",
			"sg:Wïkïpêdïyäa",
			"sc:Wikipedia",
			"sco:Wikipaedia",
			"stq:Wikipedia",
			"nso:Wikipedia",
			"tn:Wikipedia",
			"sq:Wikipedia",
			"scn:Wikipedia",
			"si:විකිපීඩියා",
			"simple:Wikipedia",
			"sd:وڪيپيڊيا",
			"ss:Wikipedia",
			"sk:Wikipédia",
			"sl:Wikipedija",
			"cu:Википєдїꙗ",
			"szl:Wikipedyjo",
			"so:Wikipedia",
			"ckb:ویکیپیدیا",
			"srn:Wikipedia",
			"sr:Википедија",
			"sh:Wikipedia",
			"fi:Wikipedia",
			"sv:Wikipedia",
			"tl:Wikipedia",
			"ta:விக்கிப்பீடியா",
			"kab:Wikipédia",
			"roa-tara:Uicchipèdie",
			"tt:Wikipedia",
			"te:వికీపీడియా",
			"tet:Wikipédia",
			"th:วิกิพีเดีย",
			"vi:Wikipedia",
			"ti:ዊኪፔዲያ",
			"tg:Википедиа",
			"tpi:Wikipedia",
			"chr:ᏫᎩᏇᏗᏯ",
			"chy:Wikipedia",
			"ve:Wikipedia",
			"tr:Vikipedi",
			"tk:Wikipediýa",
			"tw:Wikipedia",
			"udm:Википедия",
			"uk:Вікіпедія",
			"ur:ویکیپیڈیا",
			"ug:ۋىكىپېدىيە",
			"za:Vigibakgoh",
			"vec:Wikipedia",
			"vep:Vikipedii",
			"vo:Vükiped",
			"fiu-vro:Vikipeediä",
			"wa:Wikipedia",
			"zh-classical:維基大典",
			"vls:Wikipedia",
			"war:Wikipedia",
			"wo:Wikipedia",
			"wuu:维基百科",
			"ts:Wikipediya",
			"yi:וויקיפעדיע",
			"yo:Wikipedia",
			"zh-yue:維基百科",
			"diq:Wikipediya",
			"zea:Wikipedia",
			"bat-smg:Vikipedėjė",
			"zh:维基百科",
		) );
	}

	/**
	 * Test sortPrepend, sort the links in reverse
	 */
	public function testPrepend() {
		global $egWBCSettings;
		$egWBCSettings['sort'] = 'alphabetic';
		$egWBCSettings['sortPrepend'] = array(
			'zu',
			'zh-yue',
			'zh-min-nan',
			'zh-classical',
			'zh',
			'zea',
			'za',
			'yo',
			'yi',
			'xmf',
			'xal',
			'wuu',
			'wo',
			'war',
			'wa',
			'vo',
			'vls',
			'vi',
			'vep',
			'vec',
			've',
			'uz',
			'ur',
			'uk',
			'ug',
			'udm',
			'ty',
			'tw',
			'tt',
			'ts',
			'tr',
			'tpi',
			'tn',
			'tl',
			'tk',
			'ti',
			'th',
			'tg',
			'tet',
			'te',
			'ta',
			'szl',
			'sw',
			'sv',
			'su',
			'stq',
			'ss',
			'srn',
			'sr',
			'sq',
			'so',
			'sm',
			'sl',
			'sk',
			'simple',
			'si',
			'sh',
			'sg',
			'se',
			'sd',
			'sco',
			'scn',
			'sc',
			'sah',
			'sa',
			'rue',
			'ru',
			'roa-tara',
			'roa-rup',
			'ro',
			'rmy',
			'rm',
			'qu',
			'pt',
			'ps',
			'pnt',
			'pnb',
			'pms',
			'pl',
			'pih',
			'pfl',
			'pdc',
			'pcd',
			'pap',
			'pam',
			'pag',
			'pa',
			'os',
			'or',
			'oc',
			'nv',
			'nso',
			'nrm',
			'nov',
			'no',
			'nn',
			'nl',
			'new',
			'ne',
			'nds-nl',
			'nds',
			'nap',
			'nah',
			'na',
			'mzn',
			'myv',
			'my',
			'mwl',
			'mt',
			'ms',
			'mrj',
			'mr',
			'mn',
			'ml',
			'mk',
			'mi',
			'mhr',
			'mg',
			'mdf',
			'map-bms',
			'lv',
			'ltg',
			'lt',
			'lo',
			'ln',
			'lmo',
			'lij',
			'li',
			'lez',
			'lbe',
			'lb',
			'lad',
			'la',
			'ky',
			'kw',
			'ku',
			'ksh',
			'ks',
			'krc',
			'koi',
			'ko',
			'kn',
			'km',
			'kl',
			'kk',
			'kab',
			'kaa',
			'ka',
			'jv',
			'jbo',
			'ja',
			'iu',
			'it',
			'is',
			'io',
			'ilo',
			'ik',
			'ig',
			'ie',
			'id',
			'ia',
			'hy',
			'hu',
			'ht',
			'hsb',
			'hr',
			'hi',
			'he',
			'haw',
			'hak',
			'gv',
			'gu',
			'got',
			'gn',
			'glk',
			'gl',
			'gd',
			'gan',
			'gag',
			'ga',
			'fy',
			'fur',
			'frr',
			'frp',
			'fr',
			'fo',
			'fiu-vro',
			'fi',
			'ff',
			'fa',
			'ext',
			'eu',
			'et',
			'es',
			'eo',
			'eml',
			'el',
			'dv',
			'dsb',
			'diq',
			'de',
			'da',
			'cy',
			'cv',
			'cu',
			'csb',
			'cs',
			'crh',
			'co',
			'ckb',
			'chy',
			'chr',
			'ch',
			'ceb',
			'ce',
			'cdo',
			'cbk-zam',
			'ca',
			'bxr',
			'bug',
			'bs',
			'br',
			'bpy',
			'bn',
			'bm',
			'bjn',
			'bi',
			'bh',
			'bg',
			'be-x-old',
			'be',
			'bcl',
			'bat-smg',
			'bar',
			'ba',
			'az',
			'ay',
			'av',
			'ast',
			'as',
			'arz',
			'arc',
			'ar',
			'ang',
			'an',
			'am',
			'als',
			'ak',
			'af',
			'ace',
		);

		$sorted = $this->provideArray();
		WBCLangLinkHandler::sortLinks( $sorted );
		$this->assertEquals( $sorted, array(
			"zu:Wikipedia",
			"zh:维基百科",
			"zh-yue:維基百科",
			"zh-min-nan:Wikipedia",
			"zh-classical:維基大典",
			"zea:Wikipedia",
			"za:Vigibakgoh",
			"yo:Wikipedia",
			"yi:וויקיפעדיע",
			"xmf:ვიკიპედია",
			"xal:Бикипеди",
			"wuu:维基百科",
			"wo:Wikipedia",
			"wa:Wikipedia",
			"war:Wikipedia",
			"vo:Vükiped",
			"vls:Wikipedia",
			"vi:Wikipedia",
			"ve:Wikipedia",
			"vep:Vikipedii",
			"vec:Wikipedia",
			"uz:Vikipediya",
			"ur:ویکیپیڈیا",
			"uk:Вікіпедія",
			"ug:ۋىكىپېدىيە",
			"udm:Википедия",
			"ty:Vitipetia",
			"tw:Wikipedia",
			"tt:Wikipedia",
			"ts:Wikipediya",
			"tr:Vikipedi",
			"tpi:Wikipedia",
			"tn:Wikipedia",
			"tl:Wikipedia",
			"tk:Wikipediýa",
			"ti:ዊኪፔዲያ",
			"th:วิกิพีเดีย",
			"tg:Википедиа",
			"tet:Wikipédia",
			"te:వికీపీడియా",
			"ta:விக்கிப்பீடியா",
			"szl:Wikipedyjo",
			"sw:Wikipedia",
			"sv:Wikipedia",
			"su:Wikipédia",
			"stq:Wikipedia",
			"ss:Wikipedia",
			"sr:Википедија",
			"srn:Wikipedia",
			"sq:Wikipedia",
			"so:Wikipedia",
			"sm:Wikipedia",
			"sl:Wikipedija",
			"sk:Wikipédia",
			"simple:Wikipedia",
			"si:විකිපීඩියා",
			"sh:Wikipedia",
			"sg:Wïkïpêdïyäa",
			"se:Wikipedia",
			"sd:وڪيپيڊيا",
			"sc:Wikipedia",
			"sco:Wikipaedia",
			"scn:Wikipedia",
			"sah:Бикипиэдьийэ",
			"sa:विकिपीडिया",
			"ru:Википедия",
			"rue:Вікіпедія",
			"ro:Wikipedia",
			"roa-tara:Uicchipèdie",
			"roa-rup:Wikipedia",
			"rmy:Vikipidiya",
			"rm:Vichipedia",
			"qu:Wikipidiya",
			"pt:Wikipédia",
			"ps:ويکيپېډيا",
			"pnt:Βικιπαίδεια",
			"pnb:وکیپیڈیا",
			"pms:Wikipedia",
			"pl:Wikipedia",
			"pih:Wikkapedya",
			"pfl:Wikipedia",
			"pdc:Wikipedia",
			"pcd:Wikipédia",
			"pap:Wikipedia",
			"pam:Wikipedia",
			"pag:Wikipedia",
			"pa:ਵਿਕਿਪੀਡਿਆ",
			"os:Википеди",
			"or:ଉଇକିପିଡ଼ିଆ",
			"oc:Wikipèdia",
			"nv:Íiyisíí Naaltsoos",
			"nso:Wikipedia",
			"nrm:Viqùipédie",
			"no:Wikipedia",
			"nov:Wikipedie",
			"nn:Wikipedia",
			"nl:Wikipedia",
			"new:विकिपिडिया",
			"ne:विकिपीडिया",
			"nds:Wikipedia",
			"nds-nl:Wikipedie",
			"na:Wikipediya",
			"nap:Wikipedia",
			"nah:Huiquipedia",
			"mzn:ویکی‌پدیا",
			"myv:Википедиясь",
			"my:ဝီကီပီးဒီးယား",
			"mwl:Biquipédia",
			"mt:Wikipedija",
			"ms:Wikipedia",
			"mrj:Википеди",
			"mr:विकिपीडिया",
			"mn:Википедиа",
			"ml:വിക്കിപീഡിയ",
			"mk:Википедија",
			"mi:Wikipedia",
			"mhr:Википедий",
			"mg:Wikipedia",
			"mdf:Википедие",
			"map-bms:Wikipedia",
			"lv:Vikipēdija",
			"lt:Vikipedija",
			"ltg:Vikipedeja",
			"lo:ວິກິພີເດຍ",
			"ln:Wikipedia",
			"lmo:Wikipedia",
			"li:Wikipedia",
			"lij:Wikipedia",
			"lez:Википедия",
			"lb:Wikipedia",
			"lbe:Википедия",
			"la:Vicipaedia",
			"lad:Vikipedya",
			"ky:Уикипедия",
			"kw:Wikipedya",
			"ku:Wîkîpediya",
			"ks:ویکیپیٖڈیا",
			"ksh:Wikkipedija",
			"krc:Википедия",
			"koi:Википедия",
			"ko:위키백과",
			"kn:ವಿಕಿಪೀಡಿಯ",
			"km:វិគីភីឌា",
			"kl:Wikipedia",
			"kk:Уикипедия",
			"ka:ვიკიპედია",
			"kab:Wikipédia",
			"kaa:Wikipedia",
			"jv:Wikipedia",
			"jbo:uikipidias",
			"ja:ウィキペディア",
			"iu:ᐅᐃᑭᐱᑎᐊ",
			"it:Wikipedia",
			"is:Wikipedia",
			"io:Wikipedio",
			"ilo:Wikipedia",
			"ik:Uiqipitia",
			"ig:Wikipedia",
			"ie:Wikipedia",
			"id:Wikipedia",
			"ia:Wikipedia",
			"hy:Վիքիպեդիա",
			"hu:Wikipédia",
			"ht:Wikipedya",
			"hsb:Wikipedija",
			"hr:Wikipedija",
			"hi:विकिपीडिया",
			"he:ויקיפדיה",
			"haw:Wikipikia",
			"hak:Ngùi-kî pak-khô",
			"gv:Wikipedia",
			"gu:વિકિપીડિયા",
			"got:𐍅𐌹𐌺𐌹𐍀𐌴𐌳𐌾𐌰",
			"gn:Vikipetã",
			"gl:Wikipedia",
			"glk:ویکیپدیا جی",
			"gd:Uicipeid",
			"ga:Vicipéid",
			"gan:維基百科",
			"gag:Vikipediya",
			"fy:Wikipedia",
			"fur:Vichipedie",
			"fr:Wikipédia",
			"frr:Wikipedia",
			"frp:Vouiquipèdia",
			"fo:Wikipedia",
			"fi:Wikipedia",
			"fiu-vro:Vikipeediä",
			"ff:Wikipeediya",
			"fa:ویکی‌پدیا",
			"ext:Wikipédia",
			"eu:Wikipedia",
			"et:Vikipeedia",
			"es:Wikipedia",
			"eo:Vikipedio",
			"eml:Vichipedìa",
			"el:Βικιπαίδεια",
			"dv:ވިކިޕީޑިޔާ",
			"dsb:Wikipedija",
			"diq:Wikipediya",
			"de:Wikipedia",
			"da:Wikipedia",
			"cy:Wicipedia",
			"cv:Википеди",
			"cu:Википєдїꙗ",
			"cs:Wikipedie",
			"csb:Wikipedijô",
			"crh:Vikipediya",
			"co:Wikipedia",
			"ckb:ویکیپیدیا",
			"chy:Wikipedia",
			"ch:Wikipedia",
			"chr:ᏫᎩᏇᏗᏯ",
			"ce:Википедийа",
			"ceb:Wikipedya",
			"cdo:Wikipedia",
			"cbk-zam:Wikipedia",
			"ca:Viquipèdia",
			"bxr:Википеэди",
			"bug:Wikipedia",
			"bs:Wikipedia",
			"br:Wikipedia",
			"bpy:উইকিপিডিয়া",
			"bn:উইকিপিডিয়া",
			"bm:Wikipedi",
			"bjn:Wikipidia",
			"bi:Wikipedia",
			"bh:विकिपीडिया",
			"bg:Уикипедия",
			"be:Вікіпедыя",
			"be-x-old:Вікіпэдыя",
			"bcl:Wikipidya",
			"ba:Википедия",
			"bat-smg:Vikipedėjė",
			"bar:Wikipedia",
			"az:Vikipediya",
			"ay:Wikipidiya",
			"av:Википедия",
			"ast:Uiquipedia",
			"as:ৱিকিপিডিয়া",
			"ar:ويكيبيديا",
			"arz:ويكيبيديا",
			"arc:ܘܝܩܝܦܕܝܐ",
			"ang:Ƿikipǣdia",
			"an:Biquipedia",
			"am:ውክፔዲያ",
			"als:Wikipedia",
			"ak:Wikipedia",
			"af:Wikipedia",
			"ace:Wikipèdia",
		) );
	}

	/**
	 * Unknown languages go to the bottom.
	 */
	public function testUnknown() {
		global $egWBCSettings;
		$egWBCSettings['sort'] = 'alphabetic';

		$sorted = $this->provideArray();
		array_unshift( $sorted, "qzz:???" );
		WBCLangLinkHandler::sortLinks( $sorted );
		$this->assertEquals( $sorted, array(
			"ace:Wikipèdia",
			"af:Wikipedia",
			"ak:Wikipedia",
			"als:Wikipedia",
			"am:ውክፔዲያ",
			"ang:Ƿikipǣdia",
			"ar:ويكيبيديا",
			"an:Biquipedia",
			"arc:ܘܝܩܝܦܕܝܐ",
			"roa-rup:Wikipedia",
			"frp:Vouiquipèdia",
			"as:ৱিকিপিডিয়া",
			"ast:Uiquipedia",
			"gn:Vikipetã",
			"av:Википедия",
			"ay:Wikipidiya",
			"az:Vikipediya",
			"bm:Wikipedi",
			"bn:উইকিপিডিয়া",
			"bjn:Wikipidia",
			"zh-min-nan:Wikipedia",
			"map-bms:Wikipedia",
			"ba:Википедия",
			"be:Вікіпедыя",
			"be-x-old:Вікіпэдыя",
			"bh:विकिपीडिया",
			"bcl:Wikipidya",
			"bi:Wikipedia",
			"bg:Уикипедия",
			"bar:Wikipedia",
			"bs:Wikipedia",
			"br:Wikipedia",
			"bxr:Википеэди",
			"ca:Viquipèdia",
			"cv:Википеди",
			"ceb:Wikipedya",
			"cs:Wikipedie",
			"ch:Wikipedia",
			"cbk-zam:Wikipedia",
			"co:Wikipedia",
			"cy:Wicipedia",
			"da:Wikipedia",
			"pdc:Wikipedia",
			"de:Wikipedia",
			"dv:ވިކިޕީޑިޔާ",
			"nv:Íiyisíí Naaltsoos",
			"dsb:Wikipedija",
			"et:Vikipeedia",
			"el:Βικιπαίδεια",
			"eml:Vichipedìa",
			"myv:Википедиясь",
			"es:Wikipedia",
			"eo:Vikipedio",
			"ext:Wikipédia",
			"eu:Wikipedia",
			"fa:ویکی‌پدیا",
			"fo:Wikipedia",
			"fr:Wikipédia",
			"fy:Wikipedia",
			"ff:Wikipeediya",
			"fur:Vichipedie",
			"ga:Vicipéid",
			"gv:Wikipedia",
			"gag:Vikipediya",
			"gd:Uicipeid",
			"gl:Wikipedia",
			"gan:維基百科",
			"glk:ویکیپدیا جی",
			"gu:વિકિપીડિયા",
			"got:𐍅𐌹𐌺𐌹𐍀𐌴𐌳𐌾𐌰",
			"hak:Ngùi-kî pak-khô",
			"xal:Бикипеди",
			"ko:위키백과",
			"haw:Wikipikia",
			"hy:Վիքիպեդիա",
			"hi:विकिपीडिया",
			"hsb:Wikipedija",
			"hr:Wikipedija",
			"io:Wikipedio",
			"ig:Wikipedia",
			"ilo:Wikipedia",
			"bpy:উইকিপিডিয়া",
			"id:Wikipedia",
			"ia:Wikipedia",
			"ie:Wikipedia",
			"iu:ᐅᐃᑭᐱᑎᐊ",
			"ik:Uiqipitia",
			"os:Википеди",
			"zu:Wikipedia",
			"is:Wikipedia",
			"it:Wikipedia",
			"he:ויקיפדיה",
			"jv:Wikipedia",
			"kl:Wikipedia",
			"kn:ವಿಕಿಪೀಡಿಯ",
			"pam:Wikipedia",
			"krc:Википедия",
			"ka:ვიკიპედია",
			"ks:ویکیپیٖڈیا",
			"csb:Wikipedijô",
			"kk:Уикипедия",
			"kw:Wikipedya",
			"sw:Wikipedia",
			"ht:Wikipedya",
			"ku:Wîkîpediya",
			"ky:Уикипедия",
			"mrj:Википеди",
			"lad:Vikipedya",
			"lbe:Википедия",
			"lez:Википедия",
			"lo:ວິກິພີເດຍ",
			"ltg:Vikipedeja",
			"la:Vicipaedia",
			"lv:Vikipēdija",
			"lb:Wikipedia",
			"lt:Vikipedija",
			"lij:Wikipedia",
			"li:Wikipedia",
			"ln:Wikipedia",
			"jbo:uikipidias",
			"lmo:Wikipedia",
			"hu:Wikipédia",
			"mk:Википедија",
			"mg:Wikipedia",
			"ml:വിക്കിപീഡിയ",
			"mt:Wikipedija",
			"mi:Wikipedia",
			"mr:विकिपीडिया",
			"xmf:ვიკიპედია",
			"arz:ويكيبيديا",
			"mzn:ویکی‌پدیا",
			"ms:Wikipedia",
			"cdo:Wikipedia",
			"mwl:Biquipédia",
			"mdf:Википедие",
			"mn:Википедиа",
			"my:ဝီကီပီးဒီးယား",
			"nah:Huiquipedia",
			"na:Wikipediya",
			"nl:Wikipedia",
			"nds-nl:Wikipedie",
			"ne:विकिपीडिया",
			"new:विकिपिडिया",
			"ja:ウィキペディア",
			"nap:Wikipedia",
			"ce:Википедийа",
			"frr:Wikipedia",
			"pih:Wikkapedya",
			"no:Wikipedia",
			"nn:Wikipedia",
			"nrm:Viqùipédie",
			"nov:Wikipedie",
			"oc:Wikipèdia",
			"mhr:Википедий",
			"or:ଉଇକିପିଡ଼ିଆ",
			"uz:Vikipediya",
			"pa:ਵਿਕਿਪੀਡਿਆ",
			"pfl:Wikipedia",
			"pag:Wikipedia",
			"pnb:وکیپیڈیا",
			"pap:Wikipedia",
			"ps:ويکيپېډيا",
			"koi:Википедия",
			"km:វិគីភីឌា",
			"pcd:Wikipédia",
			"pms:Wikipedia",
			"tpi:Wikipedia",
			"nds:Wikipedia",
			"pl:Wikipedia",
			"pnt:Βικιπαίδεια",
			"pt:Wikipédia",
			"kaa:Wikipedia",
			"crh:Vikipediya",
			"ty:Vitipetia",
			"ksh:Wikkipedija",
			"ro:Wikipedia",
			"rmy:Vikipidiya",
			"rm:Vichipedia",
			"qu:Wikipidiya",
			"rue:Вікіпедія",
			"ru:Википедия",
			"sah:Бикипиэдьийэ",
			"se:Wikipedia",
			"sm:Wikipedia",
			"sa:विकिपीडिया",
			"sg:Wïkïpêdïyäa",
			"sc:Wikipedia",
			"sco:Wikipaedia",
			"stq:Wikipedia",
			"nso:Wikipedia",
			"tn:Wikipedia",
			"sq:Wikipedia",
			"scn:Wikipedia",
			"si:විකිපීඩියා",
			"simple:Wikipedia",
			"sd:وڪيپيڊيا",
			"ss:Wikipedia",
			"sk:Wikipédia",
			"sl:Wikipedija",
			"cu:Википєдїꙗ",
			"szl:Wikipedyjo",
			"so:Wikipedia",
			"ckb:ویکیپیدیا",
			"srn:Wikipedia",
			"sr:Википедија",
			"sh:Wikipedia",
			"su:Wikipédia",
			"fi:Wikipedia",
			"sv:Wikipedia",
			"tl:Wikipedia",
			"ta:விக்கிப்பீடியா",
			"kab:Wikipédia",
			"roa-tara:Uicchipèdie",
			"tt:Wikipedia",
			"te:వికీపీడియా",
			"tet:Wikipédia",
			"th:วิกิพีเดีย",
			"ti:ዊኪፔዲያ",
			"tg:Википедиа",
			"chr:ᏫᎩᏇᏗᏯ",
			"chy:Wikipedia",
			"ve:Wikipedia",
			"tr:Vikipedi",
			"tk:Wikipediýa",
			"tw:Wikipedia",
			"udm:Википедия",
			"bug:Wikipedia",
			"uk:Вікіпедія",
			"ur:ویکیپیڈیا",
			"ug:ۋىكىپېدىيە",
			"za:Vigibakgoh",
			"vec:Wikipedia",
			"vep:Vikipedii",
			"vi:Wikipedia",
			"vo:Vükiped",
			"fiu-vro:Vikipeediä",
			"wa:Wikipedia",
			"zh-classical:維基大典",
			"vls:Wikipedia",
			"war:Wikipedia",
			"wo:Wikipedia",
			"wuu:维基百科",
			"ts:Wikipediya",
			"yi:וויקיפעדיע",
			"yo:Wikipedia",
			"zh-yue:維基百科",
			"diq:Wikipediya",
			"zea:Wikipedia",
			"bat-smg:Vikipedėjė",
			"zh:维基百科",
			"qzz:???",
		) );
	}

	/**
	 * Provides a randomly shuffled array.
	 */
	public function provideArray() {
		//Copied from http://en.wikipedia.org/w/index.php?title=Wikipedia&oldid=488056301
		$a = array(
			"ace:Wikipèdia",
			"af:Wikipedia",
			"ak:Wikipedia",
			"als:Wikipedia",
			"am:ውክፔዲያ",
			"ang:Ƿikipǣdia",
			"ar:ويكيبيديا",
			"an:Biquipedia",
			"arc:ܘܝܩܝܦܕܝܐ",
			"roa-rup:Wikipedia",
			"frp:Vouiquipèdia",
			"as:ৱিকিপিডিয়া",
			"ast:Uiquipedia",
			"gn:Vikipetã",
			"av:Википедия",
			"ay:Wikipidiya",
			"az:Vikipediya",
			"bm:Wikipedi",
			"bn:উইকিপিডিয়া",
			"bjn:Wikipidia",
			"zh-min-nan:Wikipedia",
			"map-bms:Wikipedia",
			"ba:Википедия",
			"be:Вікіпедыя",
			"be-x-old:Вікіпэдыя",
			"bh:विकिपीडिया",
			"bcl:Wikipidya",
			"bi:Wikipedia",
			"bg:Уикипедия",
			"bar:Wikipedia",
			"bs:Wikipedia",
			"br:Wikipedia",
			"bxr:Википеэди",
			"ca:Viquipèdia",
			"cv:Википеди",
			"ceb:Wikipedya",
			"cs:Wikipedie",
			"ch:Wikipedia",
			"cbk-zam:Wikipedia",
			"co:Wikipedia",
			"cy:Wicipedia",
			"da:Wikipedia",
			"pdc:Wikipedia",
			"de:Wikipedia",
			"dv:ވިކިޕީޑިޔާ",
			"nv:Íiyisíí Naaltsoos",
			"dsb:Wikipedija",
			"et:Vikipeedia",
			"el:Βικιπαίδεια",
			"eml:Vichipedìa",
			"myv:Википедиясь",
			"es:Wikipedia",
			"eo:Vikipedio",
			"ext:Wikipédia",
			"eu:Wikipedia",
			"fa:ویکی‌پدیا",
			"fo:Wikipedia",
			"fr:Wikipédia",
			"fy:Wikipedia",
			"ff:Wikipeediya",
			"fur:Vichipedie",
			"ga:Vicipéid",
			"gv:Wikipedia",
			"gag:Vikipediya",
			"gd:Uicipeid",
			"gl:Wikipedia",
			"gan:維基百科",
			"glk:ویکیپدیا جی",
			"gu:વિકિપીડિયા",
			"got:𐍅𐌹𐌺𐌹𐍀𐌴𐌳𐌾𐌰",
			"hak:Ngùi-kî pak-khô",
			"xal:Бикипеди",
			"ko:위키백과",
			"haw:Wikipikia",
			"hy:Վիքիպեդիա",
			"hi:विकिपीडिया",
			"hsb:Wikipedija",
			"hr:Wikipedija",
			"io:Wikipedio",
			"ig:Wikipedia",
			"ilo:Wikipedia",
			"bpy:উইকিপিডিয়া",
			"id:Wikipedia",
			"ia:Wikipedia",
			"ie:Wikipedia",
			"iu:ᐅᐃᑭᐱᑎᐊ",
			"ik:Uiqipitia",
			"os:Википеди",
			"zu:Wikipedia",
			"is:Wikipedia",
			"it:Wikipedia",
			"he:ויקיפדיה",
			"jv:Wikipedia",
			"kl:Wikipedia",
			"kn:ವಿಕಿಪೀಡಿಯ",
			"pam:Wikipedia",
			"krc:Википедия",
			"ka:ვიკიპედია",
			"ks:ویکیپیٖڈیا",
			"csb:Wikipedijô",
			"kk:Уикипедия",
			"kw:Wikipedya",
			"sw:Wikipedia",
			"ht:Wikipedya",
			"ku:Wîkîpediya",
			"ky:Уикипедия",
			"mrj:Википеди",
			"lad:Vikipedya",
			"lbe:Википедия",
			"lez:Википедия",
			"lo:ວິກິພີເດຍ",
			"ltg:Vikipedeja",
			"la:Vicipaedia",
			"lv:Vikipēdija",
			"lb:Wikipedia",
			"lt:Vikipedija",
			"lij:Wikipedia",
			"li:Wikipedia",
			"ln:Wikipedia",
			"jbo:uikipidias",
			"lmo:Wikipedia",
			"hu:Wikipédia",
			"mk:Википедија",
			"mg:Wikipedia",
			"ml:വിക്കിപീഡിയ",
			"mt:Wikipedija",
			"mi:Wikipedia",
			"mr:विकिपीडिया",
			"xmf:ვიკიპედია",
			"arz:ويكيبيديا",
			"mzn:ویکی‌پدیا",
			"ms:Wikipedia",
			"cdo:Wikipedia",
			"mwl:Biquipédia",
			"mdf:Википедие",
			"mn:Википедиа",
			"my:ဝီကီပီးဒီးယား",
			"nah:Huiquipedia",
			"na:Wikipediya",
			"nl:Wikipedia",
			"nds-nl:Wikipedie",
			"ne:विकिपीडिया",
			"new:विकिपिडिया",
			"ja:ウィキペディア",
			"nap:Wikipedia",
			"ce:Википедийа",
			"frr:Wikipedia",
			"pih:Wikkapedya",
			"no:Wikipedia",
			"nn:Wikipedia",
			"nrm:Viqùipédie",
			"nov:Wikipedie",
			"oc:Wikipèdia",
			"mhr:Википедий",
			"or:ଉଇକିପିଡ଼ିଆ",
			"uz:Vikipediya",
			"pa:ਵਿਕਿਪੀਡਿਆ",
			"pfl:Wikipedia",
			"pag:Wikipedia",
			"pnb:وکیپیڈیا",
			"pap:Wikipedia",
			"ps:ويکيپېډيا",
			"koi:Википедия",
			"km:វិគីភីឌា",
			"pcd:Wikipédia",
			"pms:Wikipedia",
			"tpi:Wikipedia",
			"nds:Wikipedia",
			"pl:Wikipedia",
			"pnt:Βικιπαίδεια",
			"pt:Wikipédia",
			"kaa:Wikipedia",
			"crh:Vikipediya",
			"ty:Vitipetia",
			"ksh:Wikkipedija",
			"ro:Wikipedia",
			"rmy:Vikipidiya",
			"rm:Vichipedia",
			"qu:Wikipidiya",
			"rue:Вікіпедія",
			"ru:Википедия",
			"sah:Бикипиэдьийэ",
			"se:Wikipedia",
			"sm:Wikipedia",
			"sa:विकिपीडिया",
			"sg:Wïkïpêdïyäa",
			"sc:Wikipedia",
			"sco:Wikipaedia",
			"stq:Wikipedia",
			"nso:Wikipedia",
			"tn:Wikipedia",
			"sq:Wikipedia",
			"scn:Wikipedia",
			"si:විකිපීඩියා",
			"simple:Wikipedia",
			"sd:وڪيپيڊيا",
			"ss:Wikipedia",
			"sk:Wikipédia",
			"sl:Wikipedija",
			"cu:Википєдїꙗ",
			"szl:Wikipedyjo",
			"so:Wikipedia",
			"ckb:ویکیپیدیا",
			"srn:Wikipedia",
			"sr:Википедија",
			"sh:Wikipedia",
			"su:Wikipédia",
			"fi:Wikipedia",
			"sv:Wikipedia",
			"tl:Wikipedia",
			"ta:விக்கிப்பீடியா",
			"kab:Wikipédia",
			"roa-tara:Uicchipèdie",
			"tt:Wikipedia",
			"te:వికీపీడియా",
			"tet:Wikipédia",
			"th:วิกิพีเดีย",
			"ti:ዊኪፔዲያ",
			"tg:Википедиа",
			"chr:ᏫᎩᏇᏗᏯ",
			"chy:Wikipedia",
			"ve:Wikipedia",
			"tr:Vikipedi",
			"tk:Wikipediýa",
			"tw:Wikipedia",
			"udm:Википедия",
			"bug:Wikipedia",
			"uk:Вікіпедія",
			"ur:ویکیپیڈیا",
			"ug:ۋىكىپېدىيە",
			"za:Vigibakgoh",
			"vec:Wikipedia",
			"vep:Vikipedii",
			"vi:Wikipedia",
			"vo:Vükiped",
			"fiu-vro:Vikipeediä",
			"wa:Wikipedia",
			"zh-classical:維基大典",
			"vls:Wikipedia",
			"war:Wikipedia",
			"wo:Wikipedia",
			"wuu:维基百科",
			"ts:Wikipediya",
			"yi:וויקיפעדיע",
			"yo:Wikipedia",
			"zh-yue:維基百科",
			"diq:Wikipediya",
			"zea:Wikipedia",
			"bat-smg:Vikipedėjė",
			"zh:维基百科",
		);
		array_shuffle( $a );
		return $a;
	}

}
