{* $Id: tabs_content.post.tpl 9222 2010-04-02 13:48:38Z lexa $ *}

{assign var="data" value=$product_data}

<div id="content_reward_points" class="hidden">
<fieldset>
	{include file="common_templates/subheader.tpl" title=$lang.price_in_points}

	{assign var="is_auto" value=$addons.reward_points.auto_price_in_points}
	<div class="select-field">
		<input type="hidden" name="product_data[is_pbp]" value="N" />
		<input type="checkbox" name="product_data[is_pbp]" id="pd_is_pbp" value="Y" {if $data.is_pbp == "Y" || $mode == "add"}checked="checked"{/if} onclick="{if $is_auto != 'Y'}jQuery.disable_elms(['price_in_points'], !this.checked);{else}jQuery.disable_elms(['is_oper'], !this.checked); jQuery.disable_elms(['price_in_points'], !this.checked || !$('#is_oper').is(':checked'));{/if}" class="checkbox" />
		<label for="pd_is_pbp">{$lang.pay_by_points}</label>
	</div>

	{if $is_auto == "Y"}
	<div class="select-field">
		{math equation="x*y" x=$data.price|default:"0" y=$addons.reward_points.point_rate assign="rate_pip"}
		<input type="hidden" id="price_in_points_exchange" value="{$rate_pip|ceil}" />
		<input type="hidden" name="product_data[is_oper]" value="N" />
		<input type="checkbox" id="is_oper" name="product_data[is_oper]" value="Y" {if $data.is_oper == "Y"}checked="checked"{/if} onclick="jQuery.disable_elms(['price_in_points'], !this.checked);" {if $data.is_pbp != "Y"} disabled="disabled"{/if} class="checkbox" />
		<label for="is_oper">{$lang.override_per}</label>
	</div>
	{/if}

	<div class="select-field">
		<input type="text" id="price_in_points" name="product_data[point_price]" value="{$data.point_price|default:0}" class="input-text-medium" size="10"  {if $data.is_pbp != "Y" || ($is_auto == "Y" && $data.is_oper != "Y")}disabled="disabled"{/if} class="checkbox" />
		<label for="price_in_points">{$lang.price_in_points}</label>
	</div>
</fieldset>

<input type="hidden" name="object_type" value="{$object_type}" />
{include file="common_templates/subheader.tpl" title=$lang.earned_points}
<div class="select-field">
	<input type="hidden" name="product_data[is_op]" value="N" />
	<input type="checkbox" name="product_data[is_op]" id="rp_is_op" value="Y" {if $data.is_op == "Y"}checked="checked"{/if} onclick="jQuery.disable_elms([{foreach from=$reward_usergroups item=m}'earned_points_{$object_type}_{$m.usergroup_id}',{/foreach}{foreach from=$reward_usergroups item=m}'points_type_{$object_type}_{$m.usergroup_id}',{/foreach}], !this.checked);" class="checkbox" />
	<label for="rp_op">{$lang.override_gc_points}</label>
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
	<tr {cycle values="class=\"table-row\", " reset=1}>
		<td>&nbsp;<input type="hidden" name="product_data[reward_points][{$m.usergroup_id}][usergroup_id]" value="{$m.usergroup_id}" />
			{$m.usergroup}</td>
		<td>
			<input type="text" class="input-text-short" id="earned_points_{$object_type}_{$m.usergroup_id}" name="product_data[reward_points][{$m.usergroup_id}][amount]" value="{$reward_points[$m.usergroup_id].amount|default:"0"}" {if $data.is_op != "Y"}disabled="disabled"{/if} /></td>
		<td>
			<select id="points_type_{$object_type}_{$m.usergroup_id}" name="product_data[reward_points][{$m.usergroup_id}][amount_type]" {if $object_type == $smarty.const.PRODUCT_REWARD_POINTS && $data.is_op != 'Y'}disabled="disabled"{/if}>
				<option value="A" {if $reward_points[$m.usergroup_id].amount_type == "A"}selected{/if}>{$lang.absolute} ({$lang.points_lower})</option>
				<option value="P" {if $reward_points[$m.usergroup_id].amount_type == "P"}selected{/if}>{$lang.percent} (%)</option>
			</select>
		</td>
	</tr>
{/foreach}
</tbody>
</table>

</div>