{* $Id: update.tpl 9804 2010-06-16 13:11:34Z lexa $ *}

{script src="js/picker.js"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="queue_from" class="cm-form-highlight">
<input type="hidden" name="workflow_id" value="{$smarty.request.workflow_id}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

{capture name="tabsbox"}

<div id="content_general">

	<fieldset>
		<div class="form-field">
			<label for="elm_description" class="cm-required">{$lang.name}:</label>
			<input type="text" name="workflow_data[general][description]" id="elm_description" size="60" value="{$workflow.description}" class="input-text main-input" />
		</div>

		<div class="form-field">
			<label {if $mode == "add"}class="cm-required"{/if}>{$lang.object_type}:</label>
			{if $mode == "update"}
				{foreach from=$objects_data item="object"}
					{if $object.object == $workflow.object}{$object.title}{/if}
				{/foreach}
			{else}
			<select name="workflow_data[general][object]" id="add_object">
				{foreach from=$objects_data item="object"}
					<option value="{$object.object}" {if $object.object == $workflow.object}selected="selected"{/if}>{$object.title}</option>
				{/foreach}
			</select>
			{/if}
		</div>

		{include file="common_templates/select_status.tpl" input_name="workflow_data[general][status]" obj=$workflow}

	</fieldset>

</div>

<div id="content_queue">
	{include file="views/revisions_workflow/components/queue.tpl"}
</div>

<div id="content_select">
	{include file="views/revisions_workflow/components/select.tpl"}
</div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

<div class="buttons-container buttons-bg">
	{if $mode == "update"}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[revisions_workflow.update]"}
	{else}
		{include file="buttons/save_cancel.tpl" create=true but_name="dispatch[revisions_workflow.update]"}
	{/if}
</div>

</form>

{/capture}

{if $mode == "update"}
	{assign var="_title" value="`$lang.editing_workflow`: `$workflow.description`"}
{else}
	{assign var="_title" value=$lang.new_workflow}
{/if}

{include file="common_templates/mainbox.tpl" title=$_title content=$smarty.capture.mainbox tools=$smarty.capture.tools}