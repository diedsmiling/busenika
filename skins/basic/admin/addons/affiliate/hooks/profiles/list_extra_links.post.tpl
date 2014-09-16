{* $Id: list_extra_links.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $user.user_type == "P"}
	<li><a href="{"orders.manage?user_id=`$user.user_id`"|fn_url}">{$lang.view_all_orders}</a></li>
	<li><a href="{"profiles.act_as_user?user_id=`$user.user_id`"|fn_url}" target="_blank" >{$lang.act_on_behalf}</a></li>
{/if}