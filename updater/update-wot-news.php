<?php
require_once('../libs/simple_html_dom.php');
require_once('../conf/const.php');
require_once('../conf/db.php');
require_once('../conf/functions.php');
require_once('Mail.php');

/*
 *  mailer settings if error occurs
 */
 $mailer = array(
        'host' => 'mail.infobox.ru',
        'port' => '25',

        'uname' => 'collectd@blackdeath.ru',
        'passwd' => 'collectd_passwd1',

        'from' => 'collectd@blackdeath.ru',
        'to' => 'thunder@blackdeath.ru',

        'subject' => ' * WoT-News updater',

    );

$headers = array ('From' => $mailer['from'],
      'To' => $mailer['to'],
      'Subject' => $mailer['subject']
    );
$smtp = Mail::factory('smtp',
      array ('host' => $mailer['host'],
        'port' => $mailer['port'],
        'auth' => true,
        'username' => $mailer['uname'],
        'password' => $mailer['passwd'] )
    );
$mail_body = array();
$mail_body[] = " Start date/time ".date( "d-m-Y H:i:s",time() );

//echo get_site_domain();
/*
$def_lang = 'ru';
$cur_lang = filter_input(INPUT_GET, "lang", FILTER_SANITIZE_STRING);
if ($cur_lang == NULL || $cur_lang === false || !in_array($cur_lang, $_avail_langs) ) { $cur_lang = $def_lang; }
if ( $cur_lang != 'ru' ) { 
	putenv('LC_ALL='.get_lang());
	putenv('LANG='.get_lang());
	putenv('LANGUAGE='.get_lang());
	setlocale(LC_ALL, "en_EN");
}
*/
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
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );	
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
	if (!$content['content']) { $content['error']['text'] = "Cannot get data from World of Tanks server.\nServer returned {$response['http_code']}"; return $content; }
	
	if ($response['http_code'] == 301 || $response['http_code'] == 302)	{
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
		$content['error']['text'] = 'Cannot get data from WoT server. Site returned image/jpeg. Maybe maintanence?';
//		$response = json_encode($response);
	}
//	echo $c_type;
	curl_close($ch);
//	echo $response['content'];
	return $content;

}

foreach ( $wot_lang as $cur_lang => $val ) {
	$response = getResp("/news/?page=0&language={$cur_lang}");

	if (isset($response['error']['text'])) {
//			$mail_body[] = "Got en error. Site is not responding or in maintanence.";
			$mail_body[] = " E: Updating for {$cur_lang} has failed. ".$response['error']['text'];
			$headers['Subject'] .= " Error {$cur_lang}";
	} elseif (isset($response['content'])) {
		$html = str_get_html($response['content']);
		// deleting old records from table
		makeQuery("DELETE FROM `{$tables['wot-news']}` WHERE `lang`='{$cur_lang}' AND `server`='".get_site_domain()."';");
		$cnt = 0;
		foreach($html->find('div[class=b-imgblock b-news]') as $e) {
			$link = "http://www.worldoftanks".get_site_domain().$e->find('h5[class=b-imgblock_headerlinck] a',0)->href;
			$title = mysql_real_escape_string( $e->find('h5[class=b-imgblock_headerlinck] a',0)->innertext() );
			$description = mysql_real_escape_string( $e->find('p[class=b-imgblock_text]',0)->innertext() );
			$time = round($e->find('span[class=b-imgblock_statistic_time js-newstime]',0)->getAttribute('data-timestamp'));
			$author = "www.worldoftanks".get_site_domain();
			$category = mysql_real_escape_string( $e->find('a[class=b-imgblock_statistic_category]',0)->innertext() );
			$category_link = $e->find('a[class=b-imgblock_statistic_category]',0)->href;
			$comments = count($e->find('a[class=b-news-comment]')) ? $e->find('a[class=b-news-comment]',0)->href : '';
			$comments_link = ( !empty($comments) ? " \n\r &lt;br/&gt;&lt;a href=\"{$comments}\"&gt;"._l('comments').": {$comments} &lt;/a&gt;" : "");
			$img = $e->find('a[class=b-imgblock_ico] img',0)->src;
/*
			echo "
			{$title} @ ".date("d-m-Y H:i:s z",$time)."
			<br/>
			{$author}
			<br/>
			{$category} at {$category_link} ({$img})
			<br/>
			{$link}
			<br/>
			{$description}
			<br/>
			{$comments} at {$comments_link}
			";
			echo "<hr/>";
*/			
			makeQuery( "INSERT INTO `{$tables['wot-news']}` VALUES (null, '{$link}', '{$title}', '{$description}', '".get_site_domain()."', '{$cur_lang}', FROM_UNIXTIME({$time}), '{$comments}', '{$category}', '{$category_link}', '{$img}');" );
			$cnt++;
		}
		$html->clear();
		unset($html);
		$mail_body[] = "   * updated language {$cur_lang}; records {$cnt};";
	} else {
		$mail_body[] = " E: Undefined error! I do not know whats happened ;) Try again later!";
		$headers['Subject'] .= " Error";
	}
}
$mail_body[] = " End date/time ".date( "d-m-Y H:i:s",time() );
$mail = $smtp->send( $mailer['to'], $headers, implode("\n", $mail_body) );
?>
