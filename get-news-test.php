﻿<?php
require_once('libs/simple_html_dom.php');
require_once('libs/rss_generator.inc.php');
require_once('../conf/const.php');

error_reporting(E_ALL); ini_set('error_reporting', E_ALL);

//echo get_site_domain();

$def_lang = 'ru';
$cur_lang = filter_input(INPUT_GET, "lang", FILTER_SANITIZE_STRING);
if ($cur_lang == NULL || $cur_lang === false || !in_array($cur_lang, array('ru','eu','us','de','fr','es','pl','en','cs','sea-en','th','tr')) ) { $cur_lang = $def_lang; }
if ( $cur_lang != 'ru' ) { 
	putenv('LC_ALL='.get_lang());
	putenv('LANG='.get_lang());
	putenv('LANGUAGE='.get_lang());
	setlocale(LC_ALL, "en_EN");
}

function getResp($parr) {
	
	$cookie = tempnam ("/tmp", "CURLCOOKIE");
	
	$content = array();
	$ch = curl_init();
//	echo get_site_domain();
	curl_setopt($ch, CURLOPT_URL, "http://worldoftanks".get_site_domain().$parr);
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
	
	curl_setopt($ch, CURLOPT_REFERER, "http://worldoftanks".get_site_domain()."/news/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$content['content'] = curl_exec($ch);
	$response = curl_getinfo($ch);// var_dump( $response );
	if (!$content['content']) { $content['error']['text'] = 'Cannot get data from World of Tanks server.'; return $content; }
	
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
		$content['error']['text'] = 'Cannot get data from WoT server. Maybe maintanence?';
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
$rss_channel->link = 'http://www.worldoftanks'.get_site_domain().'/news/';
$rss_channel->description = get_descr();
$rss_channel->language = get_lang();
$rss_channel->generator = 'thunder\'s PHP RSS Feed Generator';
$rss_channel->managingEditor = 'thunder@blackdeath.ru (Alexzander O. Shevchenko)';
$rss_channel->webMaster = 'thunder@blackdeath.ru (Alexzander O. Shevchenko)';

$response = getResp("/news/?page=0&language={$cur_lang}");

if (isset($response['error']['text'])) {

		$item = new rssGenerator_item();
		$item->title = $response['error']['text'];
		$item->description = "Got en error. Site is not responding or in maintanence.";
		$item->link = "http://www.worldoftanks".get_site_domain()."/news/";
		$item->guid = "";
		$item->pubDate = date("r",time());
		$rss_channel->items[] = $item;

} elseif (isset($response['content'])) {
	$html = str_get_html($response['content']);

	foreach($html->find('div[class=b-imgblock b-news]') as $e) {
		$link = "http://www.worldoftanks".get_site_domain().$e->find('h5[class=b-imgblock_headerlinck] a',0)->href;
		$title = $e->find('h5[class=b-imgblock_headerlinck] a',0)->innertext();
		$description = $e->find('p[class=b-imgblock_text]',0)->innertext();
		$time = $e->find('span[class=b-imgblock_statistic_time js-newstime]',0)->getAttribute('data-timestamp');
		$author = "www.worldoftanks".get_site_domain();
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
