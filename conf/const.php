<?
error_reporting(0); ini_set('error_reporting', 0);

$wot_sites = array( 'ru' => '.ru', 'eu' => '.eu', 'us' => '.com', 'de' => '.eu', 'fr' => '.eu', 'es' => '.eu', 'pl' => '.eu', 'en' => '.eu', 'cs' => '.eu', 'sea-en' => '-sea.com', 'th' => '-sea.com', 'tr' => '.eu' );
$wot_lang = array( 'ru' => 'ru_RU', 'eu' => 'en_US', 'us' => 'en_US', 'de' => 'de', 'fr' => 'fr', 'es' => 'es', 'pl' => 'pl', 'en' => 'en_US', 'cs' => 'cs', 'sea-en' => 'en_US', 'th' => 'th', 'tr' => 'tr' );

$_avail_langs = array('ru','eu','us','de','fr','es','pl','en','cs','sea-en','th','tr');

$_lang = array('ru' => array(), 'en' => array());
// `comments` definition
$_lang['ru']['comments'] = 'Обсуждение';	// Russia
$_lang['en']['comments'] = 'Comments';		// English/US
$_lang['de']['comments'] = 'Kommentare';	// Germany
$_lang['fr']['comments'] = 'Commentaires';	// France
$_lang['es']['comments'] = 'Comentarios';	// Espania
$_lang['pl']['comments'] = 'Komentarze';	// Poland
$_lang['cs']['comments'] = 'Komentáře';		// Czech
$_lang['th']['comments'] = 'ความเห็น';			// Thai
$_lang['tr']['comments'] = 'Yorumlar';		// Turkey

$_lang['default']['comments'] = 'Comments';		// English/US

$_lang['ru']['title'] = 'Новости MMO World of Tanks с RU сервера';	// Russia
$_lang['en']['title'] = 'World of Tanks, News';		// English/US
$_lang['de']['title'] = 'Offizielle Nachrichten von World of Tanks EU server';	// Germany
$_lang['fr']['title'] = 'Les dernières nouvelles World of Tanks EU serveur';	// France
$_lang['es']['title'] = 'Las últimas noticias World of Tanks EU servidor';	// Espania
$_lang['pl']['title'] = 'Nowości World of Tanks EU serwer';	// Poland
$_lang['cs']['title'] = 'Novinky World Of Tanks EU serveru';		// Czech
$_lang['th']['title'] = 'ข่าวจาก World of Tanks จากเซิร์ฟเวอร์ SEA';			// Thai
$_lang['tr']['title'] = 'ขWorld of Tanks, Haberler';		// Turkey
//// additional definition
$_lang['eu']['title'] = 'Latest News from World of Tanks from EU Server';		// English/US
$_lang['us']['title'] = 'Latest News from World of Tanks from US Server';		// English/US
$_lang['sea-en']['title'] = 'Latest News from World of Tanks from SEA Server';	// SEA English

$_lang['default']['title'] = "World of Tanks, News";

$_lang['ru']['descr'] = 'Новости MMO World of Tanks с RU сервера';	// Russia
$_lang['en']['descr'] = 'World of Tanks, News';		// English/US
$_lang['de']['descr'] = 'World of tanks, Nachrichten, EU cluster';	// Germany
$_lang['fr']['descr'] = 'World of tanks, Nouvelles, EU grappe';	// France
$_lang['es']['descr'] = 'World of tanks, Noticias, EU grupo';	// Espania
$_lang['pl']['descr'] = 'World of tanks, Nowości, EU grupa';	// Poland
$_lang['cs']['descr'] = 'World of tanks, Novinky, EU clusteru';		// Czech
$_lang['th']['descr'] = 'World of tanks, News, SEA cluster';			// Thai
$_lang['tr']['descr'] = 'World of tanks, Haber, EU küme';		// Turkey
//// additional definition
$_lang['eu']['descr'] = 'World of tanks, News, EU cluster';		// English/US
$_lang['us']['descr'] = 'World of tanks, News, US cluster';		// English/US
$_lang['sea-en']['descr'] = 'World of tanks, News, EU cluster';	// SEA English

$_lang['default']['descr'] = "World of Tanks, News";


function get_title() {
	global $cur_lang, $_lang;
	$item = 'title';
	$str = (!empty($_lang[$cur_lang][$item]) ? $_lang[$cur_lang][$item] : $_lang['default'][$item]);
	/*
		switch ($cur_lang) {
		case 'eu'	: $res = 'Latest News from World of Tanks from EU Server'; break;
		case 'us'	: $res = 'Latest News from World of Tanks from US Server'; break;
		case 'fr'	: $res = 'Les dernières nouvelles World of Tanks EU serveur'; break;
		case 'de'	: $res = 'Offizielle Nachrichten von World of Tanks EU server'; break;
		case 'es'	: $res = 'Las últimas noticias World of Tanks EU servidor'; break;
		case 'pl'	: $res = 'Nowości World of Tanks EU serwer'; break;
		case 'cs'	: $res = 'Novinky World Of Tanks EU serveru'; break;
		case 'ru'	: $res = 'Новости MMO World of Tanks с RU сервера'; break;
		case 'sea-en'	: $res = 'Latest News from World of Tanks from SEA Server'; break;
		case 'th'	: $res = 'ข่าวจาก World of Tanks จากเซิร์ฟเวอร์ SEA'; break;
		case 'tr'	: $res = 'ขWorld of Tanks, Haberler'; break;
		default		: $res = 'World of Tanks, News';
		}
	 */	
	return $str;
}

function get_descr() {
	global $cur_lang, $_lang;
	$item = 'descr';
	$str = (!empty($_lang[$cur_lang][$item]) ? $_lang[$cur_lang][$item] : $_lang['default'][$item]);
	/*
		switch ($cur_lang) {
		case 'eu'	: $res = 'World of tanks, News, EU cluster'; break;
		case 'us'	: $res = 'World of tanks, News, US cluster'; break;
		case 'en'	: $res = 'World of tanks, News, EU cluster'; break;
		case 'fr'	: $res = 'World of tanks, Nouvelles, EU grappe'; break;
		case 'de'	: $res = 'World of tanks, Nachrichten, EU cluster'; break;
		case 'es'	: $res = 'World of tanks, Noticias, EU grupo'; break;
		case 'pl'	: $res = 'World of tanks, Nowości, EU grupa'; break;
		case 'cs'	: $res = 'World of tanks, Novinky, EU clusteru'; break;
		case 'ru'	: $res = 'World of Tanks, Новости'; break;
		case 'sea-en'	: $res = 'World of tanks, News, EU cluster'; break;
		case 'th'	: $res = 'World of tanks, News, SEA cluster'; break;
		case 'tr'	: $res = 'World of tanks, Haber, EU küme'; break;
		default		: $res = 'World of tanks, News';
		}
	 */	
	return $str;
}

function get_lang() {
	global $cur_lang, $wot_lang;
	$res = $wot_lang[$cur_lang];
	return $res;
}
function get_site_domain() {
	global $cur_lang, $wot_sites;	
	$res = $wot_sites[$cur_lang];	
	return $res;
}

function _l($item) {
	global $cur_lang, $_lang;
	
	$str = (!empty($_lang[$cur_lang][$item]) ? $_lang[$cur_lang][$item] : $_lang['en'][$item]);
	/*	
		switch ($cur_lang) {
		case 'eu'	: $str = $_lang['en'][$item]; break;
		case 'us'	: $str = $_lang['en'][$item]; break;
		case 'ru'	: $str = $_lang['ru'][$item]; break;
		case 'fr'	: $str = $_lang['fr'][$item]; break;
		case 'de'	: $str = $_lang['de'][$item]; break;
		case 'es'	: $str = $_lang['es'][$item]; break;
		case 'pl'	: $str = $_lang['pl'][$item]; break;
		case 'cs'	: $str = $_lang['cs'][$item]; break;
		case 'sea-en'	: $str = $_lang['en'][$item]; break;
		case 'th'	: $str = $_lang['th'][$item]; break;
		case 'tr'	: $str = $_lang['tr'][$item]; break;
		default		: $str = $_lang['en'][$item]; break;
		}
	 */	
	return $str;
}

?>