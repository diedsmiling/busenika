{* $Id: totals.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $cart.points_info.reward}
	<li>
		<em>{$lang.points}:</em>
		<strong>{$cart.points_info.reward}</strong>
	</li>
{/if}

{if $cart.points_info.in_use}
	<li>
		<em>{$lang.points_in_use}&nbsp;({$cart.points_info.in_use.points}&nbsp;{$lang.points})&nbsp;<a href="{"order_management.delete_points_in_use"|fn_url}"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="{$lang.delete}" title="{$lang.delete}" /></a>:</em>
		<strong>{include file="common_templates/price.tpl" value=$cart.points_info.in_use.cost}</strong>
	</li>
{/if}