{* $Id: update.tpl 7165 2009-03-31 12:38:30Z angel $ *}

{if $mode == "add"}
	{assign var="id" value=0}
{else}
	{assign var="id" value=$usergroup.usergroup_id}
{/if}

<div id="content_group{$id}">

<form action="{""|fn_url}" method="post" name="update_usergroups_form_{$id}" class="cm-form-highlight">

<div class="object-container">
	{capture name="tabsbox"}
		<div id="content_general_{$id}">
			<div class="form-field">
				<label class="cm-required">{$lang.usergroup}:</label>
				<input type="text" name="usergroup_data[{$id}][usergroup]" size="35" value="{$usergroup.usergroup}" class="input-text-large main-input" />
			</div>

			<div class="form-field">
				<label>{$lang.type}:</label>
				{if $smarty.const.RESTRICTED_ADMIN == 1}
				<input type="hidden" name="usergroup_data[{$id}][type]" value="C" />
				{$lang.customer}
				{else}
				<select name="usergroup_data[{$id}][type]">
					<option value="C"{if $usergroup.type == "C"} selected="selected"{/if}>{$lang.customer}</option>
					<option value="A"{if $usergroup.type == "A"} selected="selected"{/if}>{$lang.administrator}</option>
				</select>
				{/if}
			</div>

			{if $mode == "add"}
				{include file="common_templates/select_status.tpl" input_name="usergroup_data[`$id`][status]" id="usergroup_data_`$id`" obj=$usergroup hidden=true}
			{/if}
		</div>
		{hook name="usergroups:tabs_content"}{/hook}
	{/capture}
	{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox}
</div>

<div class="buttons-container">
	{if $mode == "add"}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[usergroups.add]" cancel_action="close"}
	{else}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[usergroups.update]" cancel_action="close"}
	{/if}
</div>


</form>

<!--content_group{$id}--></div>