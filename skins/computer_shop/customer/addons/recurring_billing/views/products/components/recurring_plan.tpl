{* $Id: recurring_plan.tpl 6967 2009-03-04 09:26:06Z angel $ *}

<span class="cm-reload-{$p_id}" id="recurring_plan_{$plan_item.plan_id}_update_{$p_id}">
{if $active_item && !$hide_plan_id}
	<input type="hidden" id="rb_plan_{$p_id}" name="product_data[{$p_id}][recurring_plan_id]" value="{$plan_item.plan_id}" />
{/if}
<input type="hidden" name="cart_id" value="{$subscription_object_id}" />
<input type="hidden" name="return_to" value="{$return_mode}" />

{if $plan_item.plan_id == 0}
	{assign var="plan_name" value=$lang.rb_buy_product_without_subscription}
{else}
	{assign var="plan_name" value=$plan_item.name}
{/if}
<h2 class="subheader">{if $show_radio}<input type="radio" id="recurring_plan_{$plan_item.plan_id}" class="radio" onclick="fn_change_options('{$p_id}');" value="{$plan_item.plan_id}" name="recurring_plan_id"{if $active_item} checked="checked"{/if} /> <label for="recurring_plan_{$plan_item.plan_id}">{/if}{$plan_name}{if $show_radio}</label>{/if}</h2>

{if $plan_item.plan_id != 0}

{if $smarty.capture.show_price_values}
{if $plan_item.start_duration && $plan_item.base_price != $plan_item.last_base_price}
<div class="form-field{if !$plan_item.base_price|floatval} hidden{/if}" id="line_start_recurring_price_{$p_id}_{$plan_item.plan_id}">
	<label>{$lang.rb_start_price}:</label>
	<p class="price">{include file="common_templates/price.tpl" value=$plan_item.base_price span_id="start_recurring_price_`$p_id`_`$plan_item.plan_id`" class="price"}</p>
</div>
{/if}
<div class="form-field{if !$plan_item.last_base_price|floatval} hidden{/if}" id="line_recurring_price_{$p_id}_{$plan_item.plan_id}">
	<label>{$lang.rb_price}:</label>
	<p class="price">{include file="common_templates/price.tpl" value=$plan_item.last_base_price span_id="recurring_price_`$p_id`_`$plan_item.plan_id`" class="price"}</p>
</div>
{/if}

{if $plan_item.description}
<p>{$plan_item.description|unescape}</p>
{/if}
<div class="form-field">
	<label>{$lang.rb_recurring_period}:</label>
	<strong class="lowercase">{$plan_item.period|fn_get_recurring_period_name|escape}</strong>{if $plan_item.period == "P"} - {$plan_item.by_period} {$lang.days}{/if}
</div>

{if $alt_duration && $alt_duration != $plan_item.duration}
	{assign var="plan_duration" value=$alt_duration}
{else}
	{assign var="plan_duration" value=$plan_item.duration}
{/if}

{if $plan_item.allow_change_duration == "Y"}
<div class="form-field">
	<label for="rb_plan_duration_{$plan_item.plan_id}_{$p_id}">{$lang.rb_duration}:</label>
	<input id="rb_plan_duration_{$plan_item.plan_id}_{$p_id}" class="input-text-short cm-rb-duration{if !$active_item} disabled{/if}" size="5" type="text" name="product_data[{$p_id}][recurring_duration]" value="{$plan_duration}"{if !$active_item} disabled="disabled"{/if} />
</div>
{else}
<div class="form-field">
	<label>{$lang.rb_duration}:</label>
	{$plan_duration}
</div>
{/if}

{if $plan_item.start_duration}
<div class="form-field">
	<label>{$lang.rb_start_duration}:</label>
	{$plan_item.start_duration} {if $plan_item.start_duration_type == "D"}{$lang.days}{else}{$lang.months}{/if}
</div>
{/if}

{/if}
<!--recurring_plan_{$plan_item.plan_id}_update_{$p_id}--></span>