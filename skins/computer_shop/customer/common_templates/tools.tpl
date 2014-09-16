{* $Id: tools.tpl 8751 2010-02-01 07:29:17Z angel $ *}

<{if $no_link}span{else}a{/if} class="select-link cm-combo-on cm-combination {$class}" id="sw_select_wrap_{$suffix}">{$link_text|default:"tools"}</{if $no_link}span{else}a{/if}>

<div id="select_wrap_{$suffix}" class="select-popup cm-popup-box hidden left">
	<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="" class="close-icon no-margin cm-popup-switch" />
	{$tools_list|replace:"<ul>":"<ul class=\"cm-select-list\">"}
</div>