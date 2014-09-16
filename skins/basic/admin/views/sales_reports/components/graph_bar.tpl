{* $Id: graph_bar.tpl 6893 2009-02-10 14:09:11Z angel $ *}

{math equation="floor(width / 20) + 1" assign="color" width=$value_width}
{if $color > 5}
	{assign var="color" value="5"}
{/if}
{strip}
<div class="graph-bar-border"{if $bar_width} style="width: {$bar_width};"{/if} align="left">
	<div {if $value_width > 0}class="graph-bar-{$color}" style="width: {$value_width}%;"{/if}>
		&nbsp;
	</div>
</div>
{/strip}
