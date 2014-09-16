{* $Id: table_tools.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

<div class="table-tools">
	<a href="{$href}" name="check_all" class="cm-check-items cm-on underlined">{$lang.select_all}</a>|
	<a href="{$href}" name="check_all" class="cm-check-items cm-off underlined">{$lang.unselect_all}</a>
	{*
	{if $visibility}
		|
		<a href="{$href}">{$lang.select_visible}</a>|
		<a href="{$href}">{$lang.unselect_visible}</a>
	{/if}
	&nbsp;-&nbsp;&nbsp;<strong>2</strong>&nbsp;{$lang.items_selected}*}
</div>
