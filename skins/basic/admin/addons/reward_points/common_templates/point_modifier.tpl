{* $Id: point_modifier.tpl 6483 2008-12-03 14:57:53Z zeke $ *}
{strip}
	{if $product.points_info.reward}
		({if $mod_value > 0}+{else}-{/if}{$mod_value|abs}{if $mod_type != "A"}%{/if}&nbsp;{$lang.points_lower})
	{/if}
{/strip}