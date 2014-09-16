{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
{** reward points section **}
{capture name="mainbox"}
	<div id="content_reward_points">
	<a name="reward_points"></a>
	<form action="{""|fn_url}" method="post" name="reward_points" enctype="multipart/form-data">
	<input type="hidden" name="selected_section" value="reward_points" />

	<input type="hidden" name="redirect_url" value="{$index_script}?dispatch=reward_points{if $mode}.{$mode}{/if}" />

	<input type="hidden" name="object_type" value="{$object_type}" />
	{include file="common_templates/subheader.tpl" title=$lang.earned_points}

	<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
	<tbody class="cm-first-sibling">
	<tr>
		<th>{$lang.usergroup}</th>
		<th>{$lang.amount}</th>
		<th>{$lang.amount}&nbsp;{$lang.type}</th>
	</tr>
	</tbody>
	<tbody>
	{foreach from=$reward_usergroups item=m}
		{assign var="m_id" value="`$m.usergroup_id`"}
		{assign var="point" value="`$reward_points.$m_id`"}
		<tr {cycle values="class=\"table-row\", " reset=1}>
			<td>&nbsp;
				<input type="hidden" name="reward_points[{$m_id}][usergroup_id]" value="{$m_id}" />
				{$m.usergroup}</td>
			<td>
				<input type="text" id="earned_points_{$object_type}_{$m_id}" name="reward_points[{$m_id}][amount]" value="{$point.amount|default:"0"}" class="input-text-long" /></td>
			<td>
				<select name="reward_points[{$m_id}][amount_type]" class="cm-expanded">
					<option value="A" {if $point.amount_type == "A"}selected="selected"{/if}>{$lang.absolute} ({$lang.points_lower})</option>
					<option value="P" {if $point.amount_type == "P"}selected="selected"{/if}>{$lang.percent} (%)</option>
				</select>
			</td>
		</tr>
	{/foreach}
	</tbody>
	</table>

	<div id="save_button" class="buttons-container buttons-bg">
		{include file="buttons/save.tpl" but_name="dispatch[reward_points.update]" but_role="button_main"}
	</div>

	</form>

	</div>

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.reward_points content=$smarty.capture.mainbox}


{** /reward points section **}
