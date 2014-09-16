{* $Id: list.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{assign var="discussion" value=$n.news_id|fn_get_discussion:"N"}

{if $discussion && $discussion.type != "D"}
	<p><a href="{"news.view?news_id=`$n.news_id`"|fn_url}">{$lang.more_w_ellipsis}</a></p>
{/if}