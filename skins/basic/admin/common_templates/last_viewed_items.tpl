{* $Id: last_viewed_items.tpl 9754 2010-06-08 10:31:05Z lexa $ *}

<div class="last-items-content cm-smart-position cm-popup-box hidden" id="last_edited_items">
{if $last_edited_items}
	<ul>
	{foreach from=$last_edited_items item=lnk}
		<li><a {if $lnk.icon}class="{$lnk.icon}"{/if} href="{$lnk.url|fn_url}" title="{$lnk.name|unescape}">{$lnk.name|unescape|truncate:40}</a></li>
	{/foreach}
	</ul>
	<p class="float-right"><a class="cm-ajax text-button-edit" href="{"tools.cleanup_history"|fn_url}" rev="last_edited_items">{$lang.cleanup_history}</a></p>
{else}
	<p>{$lang.no_items}</p>
{/if}
<!--last_edited_items--></div>
