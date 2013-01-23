<?
require_once('../conf/const.php');
require_once('../conf/db.php');
require_once('../conf/functions.php');

$def_lang = 'ru';
$cur_lang = filter_input(INPUT_GET, "language", FILTER_SANITIZE_STRING);
if ($cur_lang == NULL || $cur_lang === false || !in_array($cur_lang, $_avail_langs) ) { $cur_lang = $def_lang; }

echo base64_encode(serialize( makeQueryArray("SELECT id, link, title, body as `description`, `date` as `time`, `comments_link` as `comment` FROM `{$tables['wot-news']}` WHERE `lang`='{$cur_lang}' AND `server`='".get_site_domain()."'") ));

?>