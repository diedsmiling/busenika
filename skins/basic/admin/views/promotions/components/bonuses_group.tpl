{* $Id: bonuses_group.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

<ul class="promotion-group">
	
	<li class="no-node no-items {if $group}hidden{/if}">
		<p class="no-items">{$lang.no_items}</p>
	</li>

	{foreach from=$group key="k" item="bonus_data" name="bonuses"}
	<li id="container_bonus_{$k}" class="cm-row-item{if $smarty.foreach.bonuses.last} cm-last-item{/if}">
		{include file="views/promotions/components/bonus.tpl" bonus_data=$bonus_data elm_id="bonus_`$k`" prefix="promotion_data[bonuses][`$k`]"}
	</li>
	{/foreach}
	
	<li id="container_add_bonus" class="clear hidden cm-row-item">
		<div class="form-field">
			<label>&nbsp;</label>
			<select onchange="jQuery.ajaxRequest('{"promotions.dynamic?prefix="|fn_url:'A':'rel':'&'}' + escape(this.name) + '&bonus=' + this.value + '&elm_id=' + this.id, {$ldelim}result_ids: 'container_' + this.id{$rdelim})">
				<option value=""> -- </option>
				{foreach from=$schema.bonuses key="_k" item="b"}
					{if $zone|in_array:$b.zones}
						{assign var="l" value="promotion_bonus_`$_k`"}
						<option value="{$_k}">{$lang.$l}</option>
					{/if}
				{/foreach}
			</select>
		</div>
	</li>
</ul>

<div class="buttons-container">
	{include file="common_templates/tools.tpl" hide_tools=true tool_onclick="fn_promotion_add(this.id, false, 'bonus');" tool_id="add_bonus" link_text=$lang.add_bonus prefix="bottom"}
</div>
