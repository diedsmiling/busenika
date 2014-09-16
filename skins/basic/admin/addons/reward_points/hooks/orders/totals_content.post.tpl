{* $Id: totals_content.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

{if $order_info.points_info.reward}
	<li>
		<em>{$lang.points}:</em>
		<strong>{$order_info.points_info.reward}&nbsp;{$lang.points_lower}</strong>
	</li>
{/if}

{if $order_info.points_info.in_use}
	<li>
		<em>{$lang.points_in_use}&nbsp;({$order_info.points_info.in_use.points}&nbsp;{$lang.points_lower}):</em>
		<strong>{include file="common_templates/price.tpl" value=$order_info.points_info.in_use.cost}</strong>
	</li>
{/if}