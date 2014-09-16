{* $Id: update.tpl 10209 2010-07-26 13:31:28Z angel $ *}

{if $item.chain_id}
	{assign var="mode" value="update"}
{else}
	{assign var="mode" value="add"}
	{assign var="extra_mode" value="buy_together"}
{/if}

{if $item.product_id}
	{assign var="product_id" value=$item.product_id}
{else}
	{assign var="product_id" value=$product_id}
{/if}

<div id="content_group_bt_{$item.chain_id}">

<form action="{""|fn_url}" method="post" name="item_update_form_bt_{$item.chain_id}" class="cm-form-highlight" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="item_id" value="{$item.chain_id}" />
<input type="hidden" name="product_id" value="{$product_id}" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="tab_general_{$item.chain_id}" class="cm-js cm-active"><a>{$lang.general}</a></li>
			<li id="tab_products_{$item.chain_id}" class="cm-js"><a>{$lang.products}</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content" id="tabs_content_{$id}">
		<fieldset>
			<div id="content_tab_general_{$item.chain_id}">
				<div class="form-field">
					<label for="item_name_bt_{$item.chain_id}" class="cm-required">{$lang.name}:</label>
					<input type="text" name="item_data[name]" id="item_name_bt_{$item.chain_id}" size="55" value="{$item.name}" class="input-text-large main-input" />
				</div>
				
				<div class="form-field">
					<label for="item_description{$item.chain_id}">{$lang.description}:</label>
					<textarea id="item_description{$item.chain_id}" name="item_data[description]" cols="55" rows="8" class="input-textarea-long">{$item.description}</textarea>
					{include file="common_templates/wysiwyg.tpl" id="item_description`$item.chain_id`"}
				</div>
				
				<div class="form-field">
					<label for="item_date_from_bt_{$item.chain_id}">{$lang.avail_from}:</label>
					{include file="common_templates/calendar.tpl" date_id="item_date_from_bt_`$item.chain_id`" date_name="item_data[date_from]" date_val=$item.date_from|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
				</div>
				
				<div class="form-field">
					<label for="item_date_to_bt_{$item.chain_id}">{$lang.avail_till}:</label>
					{include file="common_templates/calendar.tpl" date_id="item_date_to_bt_`$item.chain_id`" date_name="item_data[date_to]" date_val=$item.date_to|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
				</div>
				
				<div class="form-field">
					<label for="item_display_in_promotions_bt_{$item.chain_id}">{$lang.display_in_promotions}:</label>
					<input type="hidden" name="item_data[display_in_promotions]" value="N" />
					<input type="checkbox" name="item_data[display_in_promotions]" id="item_display_in_promotions_bt_{$item.chain_id}" value="Y" {if $item.display_in_promotions == "Y"}checked="checked"{/if} class="checkbox" />
				</div>
				
				{include file="common_templates/select_status.tpl" input_name="item_data[status]" id="item_status_bt_`$item.chain_id`" obj=$item hidden=false}
			</div>
			
			<div id="content_tab_products_{$item.chain_id}">
				{include file="common_templates/subheader.tpl" title=$lang.combination_products"}
				
				{include file="pickers/products_picker.tpl" data_id="objects_`$item.chain_id`_" input_name="item_data[products]" item_ids=$item.products_info type="table" aoc=true colspan="7"}
				
				<ul class="statistic-list">
					<li>
						<strong><a onclick="fn_buy_together_recalculate('{$item.chain_id}');">{$lang.recalculate}</a></strong>
					</li>
					<li>
						<em>{$lang.total_cost}:</em>
						<strong>{include file="common_templates/price.tpl" value=$item.total_price span_id="total_price_`$item.chain_id`"}</strong>
					</li>
					<li>
						<em>{$lang.price_for_all}:</em>
						<strong>{include file="common_templates/price.tpl" value=$item.chain_price span_id="price_for_all_`$item.chain_id`"}</strong>
					</li>
					<li>
						<em><label for="global_discount_{$item.chain_id}">{$lang.share_discount}</label>&nbsp;({$currencies.$primary_currency.symbol}):</em>
						<input type="text" class="input-text" size="4" id="global_discount_{$item.chain_id}" onkeypress="fn_buy_together_share_discount(event, '{$item.chain_id}');" />&nbsp;<a onclick="fn_buy_together_apply_discount('{$item.chain_id}');">{$lang.apply}</a>
					</li>
				</ul>
			</div>
		</fieldset>
	</div>
</div>

<div class="buttons-container">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[buy_together.update]" cancel_action="close"}
</div>

</form>

<script type="text/javascript">
	var customer_index = '{$config.customer_index}';
	//fn_buy_together_recalculate('{$item.chain_id}', '{$product_id}');
</script>

<!--content_group_bt_{$item.chain_id}--></div>