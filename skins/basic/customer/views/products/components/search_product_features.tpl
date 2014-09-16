{* $Id: search_product_features.tpl 6967 2009-03-04 09:26:06Z angel $ *}

<label for="{$feature.feature_id}">{$feature.description}:</label>
{if $feature.feature_type == "S" || $feature.feature_type == "N"}
	<select name="feature[{$feature.feature_id}]" id="{$feature.feature_id}">
		<option value="">  --  </option>
		{assign var="f_id" value=$feature.feature_id}
		{foreach from=$feature.variants item="var"}
			<option value="{$var.variant}" {if $smarty.request.feature.$f_id==$var.variant}selected{/if}>{$var.variant}</option>
		{/foreach}
	</select>
{elseif $feature.feature_type == "C"}
	<input type="hidden" name="feature[{$feature.feature_id}]" value="" />
	<input type="checkbox" name="feature[{$feature.feature_id}]" id="{$feature.feature_id}" value="Y" {if $smarty.request.feature.$f_id == "Y"}checked="checked"{/if} class="checkbox" />
{elseif $feature.feature_type == "D"}
	{html_select_date field_array="feature[`$feature.feature_id`]" time=$feature.value|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
{else}
	<input type="text" class="input-text" name="feature[{$feature.feature_id}]" id="{$feature.feature_id}" value="{$smarty.request.feature.$f_id}" />
{/if}
