<?

### Ñïèñîê ïàðàìåòðîâ, èñïîëüçóåìûõ â âåðñòêå
# $nav_before - íàâèãàöèÿ ïî ñòðàíèöàì äî âûáðàííîé
# $nav_after - íàâèãàöèÿ ïî ñòðàíèöàì ïîñëå âûáðàííîé
# $sel_page - âûáðàííàÿ ñòðàíèöà
# $total_page - êîëè÷åñòâî ñòðàíèö

if ($total_page)
$content .='<p style="margin:17px 15px 20px 22px; text-align:right;">'.$nav_before.'<b>'.$sel_page.'</b>'.$nav_after.'<br />Всего страниц: '.$total_page.'</p>
';

?>
