{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{capture name="mainbox"}

{include file="views/pages/components/pages_search_form.tpl" dispatch="pages.manage"}

<form action="{""|fn_url}" method="post" name="pages_tree_form">
<input type="hidden" name="redirect_url" value="{$config.current_url}" />

{include file="common_templates/pagination.tpl" save_current_page=true save_current_url=true}

<div class="items-container multi-level">
	{include file="views/pages/components/pages_tree.tpl" header=true}
</div>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $pages_tree}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[pages.m_clone]" class="cm-process-items" rev="pages_tree_form">{$lang.clone_selected}</a></li>
			<li><a name="dispatch[pages.m_delete]" class="cm-process-items cm-confirm" rev="pages_tree_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[pages.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}
	
	<div class="float-right">
		{foreach from=$page_types key="_k" item="_p"}
		{include file="common_templates/tools.tpl" tool_href="pages.add?page_type=`$_k`" prefix="bottom" link_text=$lang[$_p.add_name] hide_tools=true}
		{/foreach}
	</div>
</div>

{capture name="tools"}
	{foreach from=$page_types key="_k" item="_p"}
	{include file="common_templates/tools.tpl" tool_href="pages.add?page_type=`$_k`" prefix="top" link_text=$lang[$_p.add_name] hide_tools=true}
	{/foreach}
{/capture}

</form>
{/capture}

{capture name="extra_tools"}
	{include file="buttons/button.tpl" but_text=$lang.tree but_href="pages.manage?get_tree=multi_level" but_role="tool"}&nbsp;|&nbsp;
	{foreach from=$page_types key="_k" item="_p" name="fe_p"}
	{include file="buttons/button.tpl" but_text=$lang[$_p.name] but_href="pages.manage?page_type=`$_k`" but_role="tool"}{if !$smarty.foreach.fe_p.last}&nbsp;|&nbsp;{/if}
	{/foreach}
{/capture}


{include file="common_templates/mainbox.tpl" title=$lang.pages content=$smarty.capture.mainbox title_extra=$smarty.capture.title_extra tools=$smarty.capture.tools extra_tools=$smarty.capture.extra_tools}
