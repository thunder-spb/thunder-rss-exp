<?php
require_once('simple_html_dom.php');
require_once('rss_gen/rss_generator.inc.php');

error_reporting(E_ALL); ini_set('error_reporting', E_ALL);

$wot_sites = array( 'ru' => 'ru.', 'eu' => 'eu.', 'us' => 'na.', 'en' => 'eu.' );
$wot_lang = array( 'ru' => 'ru_RU', 'eu' => 'en_US', 'us' => 'en_US', 'en' => 'en_US' );

$_lang = array('ru' => array(), 'en' => array());

$_lang['ru']['comments'] = 'Обсуждение';
$_lang['en']['comments'] = 'Comments';
$_lang['de']['comments'] = 'Kommentare';
$_lang['fr']['comments'] = 'Commentaires';
$_lang['es']['comments'] = 'Comentarios';
$_lang['pl']['comments'] = 'Komentarze';

function get_title() {
	global $cur_lang;
	switch ($cur_lang) {
		case 'eu'	: $res = 'Latest News from World of Warplanes from EU Server'; break;
		case 'us'	: $res = 'Latest News from World of Warplanes from US Server'; break;
		case 'fr'	: $res = 'Les dernières nouvelles World of Warplanes EU serveur'; break;
		case 'de'	: $res = 'Die neuesten nachrichten World of Warplanes EU server'; break;
		case 'es'	: $res = 'Las últimas noticias World of Warplanes EU servidor'; break;
		case 'pl'	: $res = 'Nowości World of Warplanes EU serwer'; break;
		case 'ru'	: $res = 'Новости MMO World of Warplanes с RU сервера'; break;
		default		: $res = 'World of Warplanes, EU Server';
	}
	return $res;
}

function get_descr() {
	global $cur_lang;
	switch ($cur_lang) {
		case 'eu'	: $res = 'World of Warplanes, News, EU cluster'; break;
		case 'us'	: $res = 'World of Warplanes, News, US cluster'; break;
		case 'en'	: $res = 'World of Warplanes, News, EU cluster'; break;
		case 'fr'	: $res = 'World of Warplanes, Nouvelles, EU grappe'; break;
		case 'de'	: $res = 'World of Warplanes, Nachrichten, EU cluster'; break;
		case 'es'	: $res = 'World of Warplanes, Noticias, EU grupo'; break;
		case 'pl'	: $res = 'World of Warplanes, Nowości, EU grupa'; break;
		case 'ru'	: $res = 'World of Warplanes, Новости'; break;
		default		: $res = 'World of Warplanes, News';
	}
	return $res;
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
	
	switch ($cur_lang) {
		case 'eu'	: $str = $_lang['en'][$item]; break;
		case 'us'	: $str = $_lang['en'][$item]; break;
		case 'ru'	: $str = $_lang['ru'][$item]; break;
		case 'fr'	: $str = $_lang['fr'][$item]; break;
		case 'de'	: $str = $_lang['de'][$item]; break;
		case 'es'	: $str = $_lang['es'][$item]; break;
		case 'pl'	: $str = $_lang['pl'][$item]; break;
		default		: $str = $_lang['en'][$item]; break;
	}
	return $str;
}

$def_lang = 'ru';
$cur_lang = filter_input(INPUT_GET, "lang", FILTER_SANITIZE_STRING);
if ( $cur_lang == NULL || $cur_lang === false || !in_array($cur_lang, array('ru','eu','us','en')) ) { $cur_lang = $def_lang; }
if ( $cur_lang != 'ru' ) { 
	putenv('LC_ALL='.get_lang());
	putenv('LANG='.get_lang());
	putenv('LANGUAGE='.get_lang());
	setlocale(LC_ALL, "en_EN");
}

//echo '11=>'.get_site_domain().$cur_lang;

function getResp($parr) {
	
	$cookie = tempnam ("/tmp", "CURLCOOKIE");
	
	$content = array();
	$ch = curl_init();
//	echo get_site_domain();
	curl_setopt($ch, CURLOPT_URL, "http://".get_site_domain()."worldofwarplanes.com".$parr);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );	
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
	    array(
		'Accept: application/json, text/javascript, text/html, */*',
		'X-Requested-With: XMLHttpRequest'
	    )
	);
	
	curl_setopt($ch, CURLOPT_REFERER, "http://".get_site_domain()."worldofwarplanes.com/news/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$content['content'] = curl_exec($ch);
	if (!$content['content']) { $content['error']['text'] = 'Cannot get data from World of Warplanes server.'; return $content; }
	$response = curl_getinfo($ch);
	
	if ($response['http_code'] == 301 || $response['http_code'] == 302)
		{
			ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");

			if ( $headers = get_headers($response['url']) )
			{
				foreach( $headers as $value )
				{
					if ( substr( strtolower($value), 0, 9 ) == "location:" )
						$content['content'] = getResp( trim( substr( $value, 9, strlen($value) ) ) );
						return $content;
				}
			}
		}
	
	if (curl_getinfo($ch, CURLINFO_CONTENT_TYPE) == 'image/jpeg') {
		$content = array();
		$content['error']['text'] = 'Cannot get data from WoWP server. Maybe maintance?';
//		$response = json_encode($response);
	}
//	echo $c_type;
	curl_close($ch);
//	echo $response['content'];
	return $content;

}

//echo get_lang();
//echo '111';
//echo get_site_domain();
$rss_channel = new rssGenerator_channel();
$rss_channel->atomLinkHref = '';
$rss_channel->title = get_title();
$rss_channel->link = "http://".get_site_domain()."worldofwarplanes.com/news/";
$rss_channel->description = get_descr();
$rss_channel->language = get_lang();
$rss_channel->generator = 'thunder\'s PHP RSS Feed Generator';
$rss_channel->managingEditor = 'thunder@blackdeath.ru (Alexzander O. Shevchenko)';
$rss_channel->webMaster = 'thunder@blackdeath.ru (Alexzander O. Shevchenko)';

$response = getResp("/news/?page=0");

if (isset($response['error']['text'])) {

		$item = new rssGenerator_item();
		$item->title = $response['error']['text'];
		$item->description = "Got en error. Site is not responding or in maintance.";
		$item->link = "http://".get_site_domain()."worldofwarplanes.com/news/";
		$item->guid = "";
		$item->pubDate = date("r",time());
		$rss_channel->items[] = $item;

} elseif (isset($response['content'])) {
	$html = str_get_html($response['content']);

	foreach($html->find('div[class=b-imgblock b-news]') as $e) {
		$link = "http://".get_site_domain()."worldofwarplanes.com".$e->find('h5[class=b-imgblock_headerlinck] a',0)->href;
		$title = $e->find('h5[class=b-imgblock_headerlinck] a',0)->innertext();
		$description = $e->find('p[class=b-imgblock_text]',0)->innertext();
		$time = $e->find('span[class=b-imgblock_statistic_time js-newstime]',0)->getAttribute('data-timestamp');
		$author = get_site_domain()."worldofwarplanes.com";
		$category = $e->find('p[class=b-imgblock_text]',0)->innertext();
		$comments = count($e->find('a[class=b-news-comment]')) ? $e->find('a[class=b-news-comment]',0)->href : false;
		$comments_link = ($comments ? " \n\r &lt;br/&gt;&lt;a href=\"{$comments}\"&gt;"._l('comments').": {$comments} &lt;/a&gt;" : "");
		
		$item = new rssGenerator_item();
		$item->title = $title;
		$item->description = "{$description} {$comments_link}";
		$item->link = "{$link}";
		$item->guid = $comments;
		$item->guid_isPermaLink = false;
		$item->pubDate = date("r",$time);
		//$item->pubDate = gmdate("D, d M Y H:i:s",$time);
		$rss_channel->items[] = $item;
		
	//	$item->pubDate = 'Tue, 07 Mar 2006 00:00:01 GMT';
		
	/*	
		echo "
		{$title} @ ".date("d-m-Y H:i:s z",$time)."
		<br/>
		{$link}
		<br/>
		{$description}
		<br/>
		{$comments}
		";
		echo "<hr/>";
	*/	
	}
	$html->clear();
	unset($html);
} else {
	$item = new rssGenerator_item();
	$item->title = "Error!";
	$item->description = "Undefined error! I do not know whats happened ;) Try again later!";
	$item->link = "";
	$item->guid = "";
	$item->pubDate = date("r",time());
	$rss_channel->items[] = $item;
}
$rss_feed = new rssGenerator_rss();
$rss_feed->encoding = 'UTF-8';
$rss_feed->version = '2.0';
header('Content-Type: text/xml');
echo $rss_feed->createFeed($rss_channel);

?>
