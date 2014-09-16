<?


### Ñïèñîê ïàðàìåòðîâ, èñïîëüçóåìûõ â âåðñòêå
# $news_id - ID íîâîñòè
# $news_img_path_small - ïóòü äî ìàëåíüêîãî èçîáðàæåíèÿ íîâîñòè
# $news_title - çàãîëîâîê íîâîñòè
# $news_text_small - êîðîòêèé òåêñò íîâîñòè
# $news_date - äàòà íîâîñòè


if ($news_img_path_small)
$img_code = '<div style="padding: 0 15px 15px 0; float:left;"><img src="'.$news_img_path_small.'" class="g" alt="'.$news_title.'"></div>';
else
$img_code = '';

$content .= '
<table style="margin: 20px 0 0 0;">
<tr><td>'.$img_code.'
<p style="margin:0px; padding:0px;"><a href="/news'.$CONFIG_WSS_ENGINE[news][uri_div].'detail'.$CONFIG_WSS_ENGINE[news][uri_div].$news_id.'.html"><b>'.$news_date.'&nbsp; - &nbsp;'.$news_title.'</b></a></p>
<p>'.$news_text_small.'
<a href="/news'.$CONFIG_WSS_ENGINE[news][uri_div].'detail'.$CONFIG_WSS_ENGINE[news][uri_div].$news_id.'.html">Подробнее >>></a></p>
</td></tr>
</table>

';



?>
