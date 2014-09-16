{* $Id: tabsbox.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{if !$active_tab}
	{assign var="active_tab" value=$smarty.request.selected_section}
{/if}

{if $navigation.tabs}

{assign var="empty_tab_ids" value=$content|empty_tabs}

{script src="js/tabs.js"}
<div class="tabs clear cm-j-tabs{if $track} cm-track{/if}">
	<ul {if $tabs_section}id="tabs_{$tabs_section}"{/if}>
	{foreach from=$navigation.tabs item=tab key=key name=tabs}
		{if ((!$tabs_section && !$tab.section) || ($tabs_section == $tab.section)) && !$key|in_array:$empty_tab_ids}
		{if !$active_tab}
			{assign var="active_tab" value=$key}
		{/if}
		<li id="{$key}" class="{if $tab.js}cm-js{elseif $tab.ajax}cm-js cm-ajax{/if}{if $key == $active_tab} cm-active{/if}"><a{if $tab.href} href="{$tab.href|fn_url}"{/if}>{$tab.title}</a></li>
		{/if}
	{/foreach}
	</ul>
</div>
<div class="cm-tabs-content clear" id="tabs_content">
	{$content}
</div>

{if $onclick}
<script>
	//<![CDATA[
	var hndl = {$ldelim}
		'tabs_{$tabs_section}': {$onclick}
	{$rdelim}
	//]]>
</script>
{/if}
{else}
	{$content}
{/if}