{* $Id: group.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{assign var="prefix_md5" value=$prefix|md5}

<input type="hidden" name="{$prefix}[fake]" value="" disabled="disabled" />

{capture name="set"}
<select name="{$prefix}[set]" class="strong">
	<option value="all" {if $group.set == "all"}selected="selected"{/if}>{$lang.all}</option>
	<option value="any" {if $group.set == "any"}selected="selected"{/if}>{$lang.any}</option>
</select>
{/capture}

{capture name="set_value"}
<select name="{$prefix}[set_value]" class="strong">
	<option value="1" {if $group.set_value == 1}selected="selected"{/if}>{$lang.true}</option>
	<option value="0" {if $group && $group.set_value == 0}selected="selected"{/if}>{$lang.false}</option>
</select>
{/capture}

<ul class="promotion-group cm-row-item">
	<li class="no-node{if $root}-root{/if}">
		{if !$root}
		<div class="float-right">
			{include file="buttons/remove_item.tpl" but_class="cm-delete-row" simple=true}
		</div>
		{/if}
		<div id="add_condition_{$prefix_md5}" class="float-right">
			{include file="common_templates/tools.tpl" hide_tools=true tool_onclick="fn_promotion_add($(this).parents('div[id^=add_condition_]').attr('id'), false, 'condition');" prefix="simple" link_text=$lang.add_condition}
			{include file="common_templates/tools.tpl" hide_tools=true tool_onclick="fn_promotion_add_group($(this).parents('div[id^=add_condition_]').attr('id'), '`$zone`');" prefix="simple" link_text=$lang.add_group}
		</div>
		<label class="strong">{$lang.group}:&nbsp;</label>
		{$lang.text_promotions_group_condition|replace:"[set]":$smarty.capture.set|replace:"[set_value]":$smarty.capture.set_value}
	</li>

	<li class="no-node no-items {if $group.conditions}hidden{/if}">
		<p class="no-items">{$lang.no_items}</p>
	</li>	

	{foreach from=$group.conditions key="k" item="condition_data" name="conditions"}
	<li id="container_condition_{$prefix_md5}_{$k}" class="cm-row-item{if $smarty.foreach.conditions.last} cm-last-item{/if}">
		{if $condition_data.set} {* this is the group *}
			{include file="views/promotions/components/group.tpl" root=false group=$condition_data prefix="`$prefix`[conditions][`$k`]" elm_id="condition_`$prefix_md5`_`$k`"}
		{else}
			{include file="views/promotions/components/condition.tpl" condition_data=$condition_data prefix="`$prefix`[conditions][`$k`]" elm_id="condition_`$prefix_md5`_`$k`"}
		{/if}
	</li>
	{/foreach}

	<li id="container_add_condition_{$prefix_md5}" class="hidden cm-row-item">
		<div class="form-field">
		<select onchange="jQuery.ajaxRequest('{"promotions.dynamic?zone=`$zone`"|fn_url:'A':'rel':'&'}&prefix=' + escape(this.name) + '&condition=' + this.value + '&elm_id=' + this.id, {$ldelim}result_ids: 'container_' + this.id{$rdelim})">
			<option value=""> -- </option>
			{foreach from=$schema.conditions key="_k" item="c"}
				{if $zone|in_array:$c.zones}
					{assign var="l" value="promotion_cond_`$_k`"}
					<option value="{$_k}">{$lang.$l}</option>
				{/if}
			{/foreach}
		</select>
		</div>
	</li>
</ul>
