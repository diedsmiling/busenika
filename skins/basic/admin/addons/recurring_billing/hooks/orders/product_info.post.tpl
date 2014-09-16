{* $Id: product_info.post.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{if $oi.extra.recurring_plan_id && !($controller == "subscriptions" && $mode == "update")}
	<div class="options-info">
		<label>{$lang.rb_recurring_plan}:</label>
		{$oi.extra.recurring_plan.name}
	</div>

	<div class="options-info">
		<label>{$lang.rb_recurring_period}:</label>
		<span class="lowercase">{$oi.extra.recurring_plan.period|fn_get_recurring_period_name|escape}</span>{if $oi.extra.recurring_plan.period == "P"} - {$oi.extra.recurring_plan.by_period} {$lang.days}{/if}
	</div>

	<div class="options-info">
		<label>{$lang.rb_duration}:</label>
		{$oi.extra.recurring_duration}
	</div>

	{if $oi.extra.recurring_plan.start_duration}
	<div class="options-info">
		<label>{$lang.rb_start_duration}:</label>
		{$oi.extra.recurring_plan.start_duration} {if $oi.extra.recurring_plan.start_duration_type == "D"}{$lang.days}{else}{$lang.months}{/if}
	</div>
	{/if}
{/if}