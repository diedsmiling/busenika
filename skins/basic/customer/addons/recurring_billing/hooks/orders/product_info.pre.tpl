{* $Id: prices_block.pre.tpl 6967 2009-03-04 09:26:06Z angel $ *}

{if $product.extra.recurring_plan_id && !($smarty.const.CONTROLLER == "subscriptions" && $smarty.const.MODE == "view")}
	<div class="product-list-field clear">
		<label>{$lang.rb_recurring_plan}:</label>
		{$product.extra.recurring_plan.name}
	</div>

	<div class="product-list-field clear">
		<label>{$lang.rb_recurring_period}:</label>
		<span class="lowercase">{$product.extra.recurring_plan.period|fn_get_recurring_period_name|escape}</span>{if $product.extra.recurring_plan.period == "P"} - {$product.extra.recurring_plan.by_period} {$lang.days}{/if}
	</div>

	<div class="product-list-field clear">
		<label>{$lang.rb_duration}:</label>
		{$product.extra.recurring_duration}
	</div>

	{if $product.extra.recurring_plan.start_duration}
	<div class="product-list-field clear">
		<label>{$lang.rb_start_duration}:</label>
		{$product.extra.recurring_plan.start_duration} {if $product.extra.recurring_plan.start_duration_type == "D"}{$lang.days}{else}{$lang.months}{/if}
	</div>
	{/if}
{/if}