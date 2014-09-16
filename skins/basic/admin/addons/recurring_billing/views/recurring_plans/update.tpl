{* $Id: update.tpl 10209 2010-07-26 13:31:28Z angel $ *}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="recurring_plan_form" class="cm-form-highlight">
<input type="hidden" name="plan_id" value="{$recurring_plan.plan_id}" />
<input type="hidden" name="selected_section" id="selected_section" value="{$smarty.request.selected_section}" />

<script type="text/javascript">
//<![CDATA[
	{literal}
	function fn_change_recurring_period(obj)
	{
		var day_inputs = $('#pay_day_holder :input');
		var val = $(obj).val();
		$('#by_period').attr('disabled', 'disabled');
		$('#by_period').parents('div').eq(0).hide();
		if (val == 'P' && day_inputs.eq(0).is(':disabled')) {
			fn_switch_pay_day_input(day_inputs, 0);
			$('#by_period').removeAttr('disabled');
			$('#by_period').parents('div').eq(0).show();
		} else if (val == 'W' && day_inputs.eq(1).is(':disabled')) {
			fn_switch_pay_day_input(day_inputs, 1);
		} else if (val != 'W' && val != 'P' && day_inputs.eq(2).is(':disabled')) {
			fn_switch_pay_day_input(day_inputs, 2);
		}
	}

	function fn_switch_pay_day_input(inp, ind)
	{
		inp.attr('disabled', 'disabled').removeAttr('id').hide();
		inp.eq(ind).removeAttr('disabled').attr('id', 'pay_day').show();
	}

	function fn_toggle_recurring_price(obj, id)
	{
		if ($(obj).val() != 'original') {
			$('#' + id).show();
		} else {
			$('#' + id).hide().val(0);
		}
	}
	{/literal}
//]]>
</script>

{capture name="tabsbox"}
<div id="content_general">
<fieldset>
	<div class="form-field">
		<label for="recurring_name" class="cm-required">{$lang.title}:</label>
		<input type="text" name="recurring_plan[name]" id="recurring_name" value="{$recurring_plan.name}" size="50" class="input-text main-input" />
	</div>

	<div class="form-field">
		<label for="recurring_period" class="cm-required">{$lang.rb_recurring_period}:</label>
		<select id="recurring_period" name="recurring_plan[period]" onchange="fn_change_recurring_period(this);">
			{foreach from=$recurring_billing_data.periods key="value_type" item="name_lang_var"}
			<option value="{$value_type}" {if $recurring_plan.period == $value_type}selected="selected"{/if}>{$lang.$name_lang_var}</option>
			{/foreach}
		</select>
	</div>

	<div class="form-field{if $recurring_plan.period != "P"} hidden{/if}">
		<label for="by_period" class="cm-required">{$lang.rb_recurring_period_value}:</label>
		<input id="by_period" type="text" name="recurring_plan[by_period]" value="{$recurring_plan.by_period}" size="10" class="input-text"{if $recurring_plan.period != "P"} disabled="disabled"{/if} />
	</div>

	<div class="form-field" id="pay_day_holder">
		<label for="pay_day" class="cm-required">{$lang.rb_pay_day}:</label>
		<input type="text" name="recurring_plan[pay_day]" value="{$recurring_plan.pay_day}" size="10" class="input-text{if $recurring_plan.period != "P"} hidden{/if}" {if $recurring_plan.period == "P"}id="pay_day"{else}disabled="disabled"{/if} />
		<select name="recurring_plan[pay_day]" {if $recurring_plan.period == "W"}id="pay_day"{else}class="hidden" disabled="disabled"{/if}>
			{section name=$key loop="7" start="0" step="1"}
			{assign var="name_lang_var" value="weekday_abr_`$smarty.section.$key.index`"}
			<option value="{$smarty.section.$key.index}"{if $recurring_plan.pay_day == $smarty.section.$key.index} selected="selected"{/if}>{$lang.$name_lang_var}</option>
			{/section}
		</select>

		<select name="recurring_plan[pay_day]" {if $recurring_plan.period == "W" || $recurring_plan.period == "P"}class="hidden" disabled="disabled"{else}id="pay_day"{/if}>
			{section name=$key loop="32" start="1" step="1"}
			<option value="{$smarty.section.$key.index}"{if $recurring_plan.pay_day == $smarty.section.$key.index} selected="selected"{/if}>{$smarty.section.$key.index}</option>
			{/section}
		</select>
	</div>

	<div class="form-field">
		<label for="recurring_price" class="cm-required">{$lang.rb_price}:</label>
		<select name="recurring_plan[price_type]" onchange="fn_toggle_recurring_price(this, 'recurring_price');">
			{foreach from=$recurring_billing_data.price item="val"}
			<option value="{$val}" {if $recurring_plan.price.type == $val}selected="selected"{/if}>{$lang.$val|strtolower}</option>
			{/foreach}
		</select>&nbsp;
		<input type="text" name="recurring_plan[price_value]" id="recurring_price" value="{$recurring_plan.price.value|default:0}" size="10" class="input-text{if $recurring_plan.price.type == "original" || !$recurring_plan.price.type} hidden{/if}" />
	</div>

	<div class="form-field">
		<label for="recurring_duration" class="cm-required">{$lang.rb_duration}:</label>
		<input type="text" name="recurring_plan[duration]" id="recurring_duration" value="{$recurring_plan.duration}" size="10" class="input-text" />
	</div>

	<div class="form-field">
		<label for="recurring_start_price">{$lang.rb_start_price}:</label>
		<select name="recurring_plan[start_price_type]" onchange="fn_toggle_recurring_price(this, 'recurring_start_price');">
			{foreach from=$recurring_billing_data.price item="val"}
			<option value="{$val}"{if $recurring_plan.start_price.type == $val} selected="selected"{/if}>{$lang.$val|strtolower}</option>
			{/foreach}
		</select>&nbsp;
		<input type="text" name="recurring_plan[start_price_value]" id="recurring_start_price" value="{$recurring_plan.start_price.value}" size="10" class="input-text{if $recurring_plan.start_price.type == "original" || !$recurring_plan.start_price.type} hidden{/if}" />
	</div>

	<div class="form-field">
		<label for="recurring_start_duration">{$lang.rb_start_duration}:</label>
		<input type="text" name="recurring_plan[start_duration]" id="recurring_start_duration" value="{$recurring_plan.start_duration}" size="10" class="input-text" />
		<select name="recurring_plan[start_duration_type]">
			<option value="D"{if $recurring_plan.start_duration_type == "D"} selected="selected"{/if}>{$lang.days}</option>
			<option value="M"{if $recurring_plan.start_duration_type == "M"} selected="selected"{/if}>{$lang.months}</option>
		</select>
	</div>

	<div class="form-field">
		<label for="recurring_note">{$lang.rb_note}:</label>
		<textarea name="recurring_plan[description]" id="recurring_note" cols="50" rows="4" class="input-textarea-long">{$recurring_plan.description}</textarea>
		{include file="common_templates/wysiwyg.tpl" id="recurring_note"}
	</div>

	{include file="common_templates/select_status.tpl" input_name="recurring_plan[status]" id="recurring_plan" obj=$recurring_plan}

	<div class="form-field">
		<label for="allow_setup_duration">{$lang.rb_allow_setup_duration}:</label>
		<input type="hidden" name="recurring_plan[allow_change_duration]" value="N" />
		<input type="checkbox" name="recurring_plan[allow_change_duration]" id="allow_setup_duration"{if $recurring_plan.allow_change_duration == "Y"} checked="checked"{/if} value="Y" class="checkbox" />
	</div>

	<div class="form-field">
		<label for="allow_unsubscribe">{$lang.rb_allow_unsubscribe}:</label>
		<input type="hidden" name="recurring_plan[allow_unsubscribe]" value="N" />
		<input type="checkbox" name="recurring_plan[allow_unsubscribe]" id="allow_unsubscribe"{if $recurring_plan.allow_unsubscribe == "Y"} checked="checked"{/if} value="Y" class="checkbox" />
	</div>

	<div class="form-field">
		<label for="allow_free_buy">{$lang.rb_allow_buy_without_subscription}:</label>
		<input type="hidden" name="recurring_plan[allow_free_buy]" value="N" />
		<input type="checkbox" name="recurring_plan[allow_free_buy]" id="allow_free_buy"{if $recurring_plan.allow_free_buy == "Y"} checked="checked"{/if} value="Y" class="checkbox" />
	</div>
</fieldset>
</div>

<div id="content_linked_products" class="hidden">
	{include file="pickers/products_picker.tpl" input_name="recurring_plan[product_ids]" data_id="added_products" item_ids=$recurring_plan.product_ids type="links"}
</div>
{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[recurring_plans.update]"}
</div>
</form>

{if $mode == "add"}
	{assign var="title" value=$lang.rb_new_plan}
{else}
	{assign var="title" value="`$lang.rb_editing_plan`: `$recurring_plan.name`"}
{/if}

{/capture}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true}
