{* $Id: manage.tpl 10288 2010-07-30 12:56:02Z klerik $ *}

{script src="js/picker.js"}
{script src="js/tabs.js"}

{capture name="mainbox"}

<div id="static_data_list">
{if $section_data.multi_level == true}
	<form action="{""|fn_url}" method="post" name="static_data_tree_form">
	<input name="section" type="hidden" value="{$section}" />
		<div class="items-container multi-level">
			{include file="views/static_data/components/multi_list.tpl" items=$static_data header=true}
		</div>
{else}
	{include file="views/static_data/components/single_list.tpl}
{/if}
<!--static_data_list--></div>

<div class="buttons-container">
	{if $section_data.multi_level == true}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[static_data.m_delete]" class="cm-process-items cm-confirm" rev="static_data_tree_form">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[static_data.m_update]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	</form>
	{/if}
	{capture name="tools"}
		{capture name="add_new_picker"}
			{include file="views/static_data/update.tpl" mode="add" static_data=""}
		{/capture}
		{include file="common_templates/popupbox.tpl" id="add_new_section" text=$lang[$section_data.add_title] content=$smarty.capture.add_new_picker link_text=$lang[$section_data.add_button] act="general"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_section" text=$lang[$section_data.add_title] link_text=$lang[$section_data.add_button] act="general"}
</div>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang[$section_data.mainbox_title] content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}