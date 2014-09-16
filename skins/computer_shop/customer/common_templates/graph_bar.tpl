{* $Id: graph_bar.tpl 9141 2010-03-23 13:22:48Z alexions $ *}

<table cellpadding="0" cellspacing="0" border="0" width="{$bar_width}" class="graph-border">
<tr>
	{if $value_width > 0}
	<td class="graph-bg" width="{$value_width}%"><img src="{$images_dir}/spacer.gif" width="1" height="7" border="0" alt="" /></td>
	{/if}
	{if $value_width < 100}
	<td><img src="{$images_dir}/spacer.gif" width="1" height="7" border="0" alt="" /></td>
	{/if}
</tr>
</table>