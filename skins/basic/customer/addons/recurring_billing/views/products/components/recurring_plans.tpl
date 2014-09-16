{* $Id: recurring_plans.tpl 6967 2009-03-04 09:26:06Z angel $ *}

<div id="content_subscribtion">

{if !$hide_common_inputs}
<form{if $settings.DHTML.ajax_add_to_cart == "Y" && !$no_ajax} class="cm-ajax"{/if} action="{""|fn_url}" method="post" name="product_form_{$product.product_id}" enctype="multipart/form-data">
<input type="hidden" name="result_ids" value="cart_status,wish_list" />
{if !$stay_in_cart}
<input type="hidden" name="redirect_url" value="{$config.current_url}" />
{/if}
<input type="hidden" name="product_data[{$product.product_id}][product_id]" value="{$product.product_id}" />
{if $product.is_edp == "Y"}
<input type="hidden" name="product_data[{$product.product_id}][is_edp]" value="Y" />
{/if}
{/if}

{if $smarty.capture.product_options}
	{$smarty.capture.product_options}
{else}
	{assign var="product_options" value="product_options_`$obj_id`"}
	{$smarty.capture.$product_options}
	
	{assign var="advanced_options" value="advanced_options_`$obj_id`"}
	{$smarty.capture.$advanced_options}
{/if}

{if $product.recurring_plan_id}
	{assign var="selected_plan_id" value=$product.recurring_plan_id}
{/if}

{assign var="plan_cnt" value=$product.recurring_plans|sizeof}
{if $plan_cnt > 1}
	{assign var="show_radiobutton" value=true}
{/if}
{foreach from=$product.recurring_plans item="plan_item" name="rec_plan"}
	{assign var="s_plan_id" value=false}
	{if $plan_item.plan_id == $selected_plan_id}
		{assign var="alt_d" value=$alt_recurring_duration}
		{assign var="s_plan_id" value=true}
	{else}
		{assign var="alt_d" value=0}
		{if !$selected_plan_id && $smarty.foreach.rec_plan.first}
			{assign var="s_plan_id" value=true}
		{/if}
	{/if}
	{include file="addons/recurring_billing/views/products/components/recurring_plan.tpl" plan_item=$plan_item show_radio=$show_radiobutton p_id=$product.product_id alt_duration=$alt_d active_item=$s_plan_id}
{/foreach}

{if !$hide_common_inputs}
{$smarty.capture.buttons}

</form>
{/if}

</div>
<script type="text/javascript">
//<![CDATA[
{literal}
function fn_change_recurring_plan(obj, prod_id)
{
	var plan_id = $(obj).val();

	$('#rb_plan_' + prod_id).val(plan_id);
	price[prod_id] = recurring_plan[prod_id][plan_id]['price'];

	fn_check_exceptions(prod_id);

	$('.cm-rb-duration').attr('disabled', 'disabled');
	$('.cm-rb-duration').addClass('disabled');
	$('#rb_plan_duration_' + plan_id + '_' + prod_id).removeAttr('disabled');
	$('#rb_plan_duration_' + plan_id + '_' + prod_id).removeClass('disabled');
}
{/literal}
//]]>
</script>