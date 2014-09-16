{* $Id: tabs_content.post.tpl 9141 2010-03-23 13:22:48Z alexions $ *}
{** reward points section **}
<div class="hidden" id="content_reward_points">
<fieldset>
	<input type="hidden" name="object_type" value="{$object_type}" />

	{include file="common_templates/subheader.tpl" title=$lang.earned_points}
	<div class="select-field">
		<input type="hidden" name="category_data[is_op]" value="N" />
		<input type="checkbox" name="category_data[is_op]" id="rp_is_op" value="Y" {if $category_data.is_op == "Y"}checked="checked"{/if} onclick="jQuery.disable_elms([{foreach from=$reward_usergroups item=m}'earned_points_{$object_type}_{$m.usergroup_id}',{/foreach}{foreach from=$reward_usergroups item=m}'points_type_{$object_type}_{$m.usergroup_id}',{/foreach}], !this.checked);" class="checkbox" />
		<label for="rp_is_op">{$lang.override_g_points}</label>
	</div>

	<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
	<tbody class="cm-first-sibling">
	<tr>
		<th>{$lang.usergroup}</th>
		<th>{$lang.amount}</th>
		<th>{$lang.amount_type}</th>
	</tr>
	</tbody>
	<tbody>
	{foreach from=$reward_usergroups item=m}
		{assign var="m_id" value="`$m.usergroup_id`"}
		{assign var="point" value="`$reward_points.$m_id`"}
		<tr {cycle values="class=\"table-row\", " reset=1}>
			<td>&nbsp;
				<input type="hidden" name="category_data[reward_points][{$m_id}][usergroup_id]" value="{$m_id}" />
				{$m.usergroup}</td>
			<td>
				<input type="text" class="input-text-medium" id="earned_points_{$object_type}_{$m_id}" name="category_data[reward_points][{$m_id}][amount]" value="{$point.amount|default:"0"}" {if $category_data.is_op != "Y"}disabled="disabled"{/if} /></td>
			<td>
				<select id="points_type_{$object_type}_{$m_id}" name="category_data[reward_points][{$m_id}][amount_type]" {if $object_type == $smarty.const.CATEGORY_REWARD_POINTS && $category_data.is_op != 'Y'}disabled="disabled"{/if}>
					<option value="A" {if $point.amount_type == "A"}selected{/if}>{$lang.absolute} ({$lang.points_lower})</option>
					<option value="P" {if $point.amount_type == "P"}selected{/if}>{$lang.percent} (%)</option>
				</select></td>

		</tr>
	{/foreach}
	</tbody>
	</table>
</fieldset>
</div>

{** /reward points section **}