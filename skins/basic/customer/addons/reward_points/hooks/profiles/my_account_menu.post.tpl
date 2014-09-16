{* $Id: my_account_menu.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $auth.user_id}
<li><a href="{"reward_points.userlog"|fn_url}">{$lang.my_points}:&nbsp;<strong>{$user_info.points|default:"0"}</strong></a></li>
{/if}