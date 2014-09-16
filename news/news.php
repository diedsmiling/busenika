<?

@include_once($_SERVER['DOCUMENT_ROOT'].'/wssengine/config/site_config.php');

if (function_exists("wss_mysql_connect"))
wss_mysql_connect();


###WSENGINE###

@include($_SERVER['DOCUMENT_ROOT'].'/news/news.config.php');

@include($_SERVER['DOCUMENT_ROOT'].'/wssengine/config/news_config.php');
@include($_SERVER['DOCUMENT_ROOT'].'/wssengine/function/news/function.php');


$content = '';


$REQUEST_URI_PARSE_FOR_NEWS = uri_format();

if(!isset($from_date))
	$from_date = NULL;
if(!isset($to_date))
	$to_date = NULL;

if ($REQUEST_URI_PARSE_FOR_NEWS[1] == 'list')
$content .= news_list_announcements($CONFIG_WSS_ENGINE["news"]["quantity_in_page"], $from_date, $to_date);
elseif ($REQUEST_URI_PARSE_FOR_NEWS[1] == 'detail')
$content .=  news_detail($REQUEST_URI_PARSE_FOR_NEWS[2]);
else
{
header('HTTP/1.1 301 Moved Permanently');
header('Location: http://'.$_SERVER["HTTP_HOST"].'/404.html');
exit;
}


?>



<?

@include_once($_SERVER['DOCUMENT_ROOT'].'/wssengine/template/'.$config_page["template"].'/top.php');

?>
<!--CONTENT_START-->

<?

echo $content;

?>

<!--CONTENT_END-->
<?

@include_once($_SERVER['DOCUMENT_ROOT'].'/wssengine/template/'.$config_page["template"].'/bottom.php');

if (function_exists("wss_mysql_connect"))
mysql_close();
?>
