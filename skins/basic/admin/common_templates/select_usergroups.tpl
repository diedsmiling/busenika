{* $Id: select_usergroups.tpl 7538 2009-05-27 15:55:45Z lexa $ *}
{if $usergroup_ids !== ""}
{assign var="ug_ids" value=","|explode:$usergroup_ids}
{/if}

<input type="hidden" name="{$name}" value="" {$input_extra}/>
{foreach from=""|fn_get_default_usergroups item=usergroup}
	{if $list_mode}<p>{/if}
	<input type="checkbox" name="{$name}[]" id="{$id}_{$usergroup.usergroup_id}"{if ($ug_ids && $usergroup.usergroup_id|in_array:$ug_ids) || (!$ug_ids && $usergroup.usergroup_id == $smarty.const.USERGROUP_ALL)} checked="checked"{/if} class="checkbox" value="{$usergroup.usergroup_id}" {$input_extra}{if (!$ug_ids || ($ug_ids && $ug_ids|count == 1 && $usergroup.usergroup_id|in_array:$ug_ids)) && $usergroup.usergroup_id == $smarty.const.USERGROUP_ALL} disabled="disabled"{/if} onclick="fn_switch_default_box(this, '{$id}', {$smarty.const.USERGROUP_ALL});" />
	<label for="{$id}_{$usergroup.usergroup_id}">{$usergroup.usergroup|escape}</label>
	{if $list_mode}</p>{/if}
{/foreach}

{foreach from=$usergroups item=usergroup}
	{if $list_mode}<p>{/if}
	<input type="checkbox" name="{$name}[]" id="{$id}_{$usergroup.usergroup_id}"{if $ug_ids && $usergroup.usergroup_id|in_array:$ug_ids} checked="checked"{/if} class="checkbox" value="{$usergroup.usergroup_id}" {$input_extra} onclick="fn_switch_default_box(this, '{$id}', {$smarty.const.USERGROUP_ALL});" />
	<label for="{$id}_{$usergroup.usergroup_id}">{$usergroup.usergroup|escape}</label>
	{if $list_mode}</p>{/if}
{/foreach}

{if !"SMARTY_USERGROUPS_LOADED"|defined}
{assign var="tmp" value="SMARTY_USERGROUPS_LOADED"|define:true}
<script type="text/javascript">
	//<![CDATA[
	{literal}
	function fn_switch_default_box(holder, prefix, default_id)
	{
		var default_box = $('#' + prefix + '_' + default_id);
		var checked_items = $('input[id^=' + prefix + '_].checkbox:checked').not(default_box).length + holder.checked ? 1 : 0;
		if (checked_items == 0) {
			default_box.attr('disabled', 'disabled');
			default_box.attr('checked', 'checked');
		} else {
			default_box.removeAttr('disabled');
		}
	}
	{/literal}
	//]]>
</script>
{/if}
