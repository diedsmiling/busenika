{* $Id: list_extra_links.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $user.user_type == "C"}
	<li><a href="{"reward_points.userlog?user_id=`$user.user_id`"|fn_url}">{$lang.points} ({if $user.points}{$user.points|@unserialize}{else}0{/if})</a></li>
{/if}