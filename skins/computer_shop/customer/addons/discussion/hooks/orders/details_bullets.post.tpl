{* $Id: details_bullets.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{assign var="discussion" value=$order_info.order_id|fn_get_discussion:"O"}
{if $addons.discussion.order_initiate == "Y" && !$discussion}
	<li><a href="{"orders.initiate_discussion?order_id=`$order_info.order_id`"|fn_url}">{$lang.start_communication}</a></li>
{/if}