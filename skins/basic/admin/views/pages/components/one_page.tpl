{* $Id: one_page.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
<div class="search-result">
	<strong>{$page.result_number}.</strong> <a href="{"pages.update?page_id=`$page.page_id`"|fn_url}>{$page.page|unescape}</a>
	{assign var="more_link" value="pages.update?page_id=`$page.page_id`"|fn_url}
	<p>{$page.description|unescape|strip_tags|truncate:280:"<a href=\"`$more_link`\" >`$lang.more_link`</a>"}</p>
</div>