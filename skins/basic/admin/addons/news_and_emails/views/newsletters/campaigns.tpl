{* $Id: campaigns.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="update_campaign_form_{$id}" class="cm-form-highlight">

{include file="common_templates/pagination.tpl" save_current_page=true}

<table cellpadding="0" cellspacing="0" class="table" width="100%">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="100%">{$lang.name}</th>
	<th>{$lang.status}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$campaigns item="c"}
<tr>
	<td class="center" width="1%">
		<input type="checkbox" name="campaign_ids[]" value="{$c.campaign_id}" class="checkbox cm-item" /></td>
	<td>
		<input type="text" name="campaigns[{$c.campaign_id}][name]" value="{$c.object}" class="input-text-large" /></td>
	<td class="nowrap">
		{include file="common_templates/select_popup.tpl" id=$c.campaign_id status=$c.status hidden=false object_id_name="campaign_id" table="newsletter_campaigns"}</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-confirm" href="{"newsletters.delete_campaign?campaign_id=`$c.campaign_id`"|fn_url}">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$c.campaign_id tools_list=$smarty.capture.tools_items id="campaign_stats_`$c.campaign_id`" text=$lang.campaign_stats link_text=$lang.campaign_stats act="edit" href="newsletters.campaign_stats?campaign_id=`$c.campaign_id`" link_class="tool-link" popup=true}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container buttons-bg">
	{if $campaigns}
	<div class="float-left">
		{capture name="tools_list"}
		<ul>
			<li><a name="dispatch[newsletters.m_delete_campaigns]" class="cm-process-items cm-confirm" rev="update_campaign_form_{$id}">{$lang.delete_selected}</a></li>
		</ul>
		{/capture}
		{include file="buttons/save.tpl" but_name="dispatch[newsletters.m_update_campaigns]" but_role="button_main"}
		{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
	</div>
	{/if}

	<div class="float-right">
		{include file="common_templates/popupbox.tpl" id="add_new_campaign" text=$lang.add_new_campaign link_text=$lang.add_campaign act="general" content=""}
	</div>
</div>

</form>


{capture name="tools"}
	{capture name="add_new_picker"}
	<form action="{""|fn_url}" method="post" name="add_campaign_form">
	<div class="object-container">
		<div class="tabs cm-j-tabs">
			<ul>
				<li id="tab_steps_new" class="cm-js cm-active"><a>{$lang.general}</a></li>
			</ul>
		</div>

		<div class="cm-tabs-content" id="content_tab_steps_new">
		<fieldset>
			<div class="form-field">
				<label class="cm-required" for="c_name">{$lang.name}:</label>
				<input type="text" id="c_name" name="campaign_data[name]"  value="" class="input-text-large main-input" size="60" />
			</div>

			{include file="common_templates/select_status.tpl" input_name="campaign_data[status]" id="c_status"}

		</fieldset>
		</div>
	</div>

	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" but_name="dispatch[newsletters.add_campaign]" create=true cancel_action="close" text=$lang.add_campaign}
	</div>

	</form>
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_new_campaign" text=$lang.new_campaign link_text=$lang.add_campaign act="general" content=$smarty.capture.add_new_picker}
{/capture}



{/capture}
{include file="common_templates/mainbox.tpl" title="`$lang.newsletters`: <span class=\"lowercase\">`$lang.campaigns`</span>" content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}