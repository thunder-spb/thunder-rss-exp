<?php
header('Content-Type: text/html; charset=UTF-8');

require_once('simple_html_dom.php');

function getResp($parr) {

	$response = array();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://worldoftanks.ru".$parr);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
	    array(
		'Accept: application/json, text/javascript, text/html, */*',
		'X-Requested-With: XMLHttpRequest'
	    )
	);
	
	curl_setopt($ch, CURLOPT_REFERER, "http://worldoftanks.ru/news/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response['content'] = curl_exec($ch);
	if (!$response['content']) { $response['error']['text'] = 'Не возможно получить данные с сервера World of Tanks.'; return $response; }
	$c_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
	if (curl_getinfo($ch, CURLINFO_CONTENT_TYPE) == 'image/jpeg') {
		$response = array();
		$response['error']['text'] = 'Данные с сервера не получены. Возможно на сервере профилактика.';
	}
	curl_close($ch);
	return $response;

}

$response = getResp("/news/?page=0");

if (isset($response['error']['text'])) {

		echo $response['error']['text'];

} elseif (isset($response['content'])) {
	$html = str_get_html($response['content']);

	foreach($html->find('div[class=b-imgblock b-news]') as $e) {
		$link = "http://www.worldoftanks.ru".$e->find('h5[class=b-imgblock_headerlinck] a',0)->href;
		$title = $e->find('h5[class=b-imgblock_headerlinck] a',0)->innertext();
		$description = $e->find('p[class=b-imgblock_text]',0)->innertext();
		$time = $e->find('span[class=b-imgblock_statistic_time js-newstime]',0)->getAttribute('data-timestamp');
		$author = "www.worldoftanks.ru";
		$category = $e->find('p[class=b-imgblock_text]',0)->innertext();
		$comments = count($e->find('a[class=b-news-comment]')) ? $e->find('a[class=b-news-comment]',0)->href : '';
		
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
/*	*/	
	}
	$html->clear();
	unset($html);
} else {
	echo "unknown error :)";
}

?>
