{* $Id: tabsbox.tpl 9807 2010-06-18 07:39:35Z lexa $ *}
{if !$active_tab}
	{assign var="active_tab" value=$smarty.request.selected_section}
{/if}

{assign var="empty_tab_ids" value=$content|empty_tabs}

{if $navigation.tabs}
{script src="js/tabs.js"}
<div class="tabs cm-j-tabs{if $track} cm-track{/if}">
	<ul>
	{foreach from=$navigation.tabs item=tab key=key name=tabs}
		{if (!$tabs_section || $tabs_section == $tab.section) && !$key|in_array:$empty_tab_ids}
		<li id="{$key}{$id_suffix}" class="{if $tab.js}cm-js{elseif $tab.ajax}cm-js cm-ajax{/if}{if $key == $active_tab} cm-active{/if}"><a {if $tab.href}href="{$tab.href|fn_url}"{/if}>{$tab.title}</a>{if $key == $active_tab}{$active_tab_extra}{/if}</li>
		{/if}
	{/foreach}
	{if ($extra_export == 1)}
	<li><a href="/zenon.php?dispatch=exim.export_buytogether">Комбинации товаров(А здесь скидки!)</a></li>
	<li><a href="/zenon.php?dispatch=exim.export_simple">Product ID/Product Code</a></li>
	{/if}
	</ul>
</div>
<div class="cm-tabs-content">
	{$content}
</div>
{else}
	{$content}
{/if}
