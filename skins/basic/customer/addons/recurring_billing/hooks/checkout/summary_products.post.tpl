{* $Id: summary_products.post.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{if $product.extra.recurring_plan_id}
	<div class="product-list-field clear">
		<label>{$lang.rb_recurring_plan}:</label>
		{assign var="opt_combination" value=$product.selected_options|fn_get_options_combination}
		{$product.extra.recurring_plan.name} (<a href="{"products.view?product_id=`$product.product_id`&amp;cart_id=`$key`&amp;combination=`$opt_combination`&amp;return_to=`$smarty.const.MODE`"|fn_url}">{$lang.rb_edit_subscription}</a>)
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
{elseif $product.recurring_plans}
	{assign var="opt_combination" value=$product.selected_options|fn_get_options_combination}
	<p>{include file="buttons/button.tpl" but_text=$lang.rb_add_subscription but_href="products.view?product_id=`$product.product_id`&amp;cart_id=`$key`&amp;combination=`$opt_combination`&amp;return_to=`$smarty.const.MODE`" but_role="text"}</p>
{/if}