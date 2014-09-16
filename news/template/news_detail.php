<?

### Ñïèñîê ïàðàìåòðîâ, èñïîëüçóåìûõ â âåðñòêå
# $news_id - ID íîâîñòè
# $news_img_path - ïóòü äî èçîáðàæåíèÿ íîâîñòè
# $news_title - çàãîëîâîê íîâîñòè
# $news_text - êîðîòêèé òåêñò íîâîñòè
# $news_date - äàòà íîâîñòè

if ($news_img_path)
$img_code = '<div style="float:left; padding: 0 15px 15px 0;"><img src="'.$news_img_path.'" align=left class="g" align="left" border="1" alt="'.$news_title.'"></div>';
else
$img_code = '';

$content .='
<div style="padding: 20px 15px 15px 0">
'.$img_code.'
<p style="margin:0px; padding:0px;"><b>'.$news_date.'&nbsp; - &nbsp;'.$news_title.'</b></p>
<p>'.$news_text.'</p>
<p><a href="/news'.$CONFIG_WSS_ENGINE[news][uri_div].'list.html">Лента новостей >></a></p>
</div>
';



?>
