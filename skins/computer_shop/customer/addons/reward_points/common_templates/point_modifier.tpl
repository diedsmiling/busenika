{* $Id: point_modifier.tpl 6967 2009-03-04 09:26:06Z angel $ *}
{strip}
	{if $product.points_info.reward}
		({if $mod_value > 0}+{else}-{/if}{$mod_value|abs}{if $mod_type != "A"}%{/if}&nbsp;{$lang.points_lower})
	{/if}
{/strip}