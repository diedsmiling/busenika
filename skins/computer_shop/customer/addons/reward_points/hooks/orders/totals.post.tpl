{* $Id: totals.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $order_info.points_info.reward}
	<tr>
		<td><strong>{$lang.points}:&nbsp;</strong></td>
		<td>{$order_info.points_info.reward}&nbsp;{$lang.points_lower}</td>
	</tr>
{/if}
{if $order_info.points_info.in_use}
	<tr>
		<td><strong>{$lang.points_in_use}</strong>&nbsp;({$order_info.points_info.in_use.points}&nbsp;{$lang.points_lower})&nbsp;<strong>:</strong></td>
		<td>{include file="common_templates/price.tpl" value=$order_info.points_info.in_use.cost}</td>
	</tr>
{/if}