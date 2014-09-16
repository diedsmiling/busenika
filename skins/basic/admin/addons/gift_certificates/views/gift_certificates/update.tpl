{* $Id: update.tpl 10402 2010-08-12 08:18:09Z klerik $ *}

<script type="text/javascript">
//<![CDATA[
{literal}
function fn_check_amount()
{
	var max = parseInt((parseFloat(max_amount) / parseFloat(currencies.secondary.coefficient))*100)/100;
	var min = parseInt((parseFloat(min_amount) / parseFloat(currencies.secondary.coefficient))*100)/100;

	is_check = ($('input:checked[name="gift_cert_data[amount_type]"]').val() == 'I') ? true : false;
	if(is_check && $('#gift_cert_amount')){
		var amount = parseFloat($('#gift_cert_amount').val());
		if(amount < min || isNaN(amount) || amount > max){
			$('#gift_cert_amount').removeClass('input-text');
			$('#gift_cert_amount').addClass('failed-field');
			alert(amount_alert);
		}else{
			$('#gift_cert_amount').removeClass('failed-field');
			$('#gift_cert_amount').addClass('input-text');
		}
		return ((amount <= max) && (amount >= min) && !isNaN(amount)) ? true : false;
	}
	return true;
}
function fn_giftcert_form_elements_disable(dsbl, enbl)
{
	if(!$('form[name="gift_certificates_form"]').get(0)){
		return false;
	}
	$(':input', '#'+dsbl).attr('disabled', 'disabled');
	$(':input', '#'+enbl).removeAttr('disabled');
}
//]]>
{/literal}
</script>
{assign var="min_amount" value=$addons.gift_certificates.min_amount|escape:javascript|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:$currencies.$secondary_currency.decimals_separator:$currencies.$secondary_currency.thousands_separator:$currencies.$secondary_currency.coefficient}
{assign var="max_amount" value=$addons.gift_certificates.max_amount|escape:javascript|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:$currencies.$secondary_currency.decimals_separator:$currencies.$secondary_currency.thousands_separator:$currencies.$secondary_currency.coefficient}

{include file="views/profiles/components/profiles_scripts.tpl"}

<script type="text/javascript">
//<![CDATA[
var default_state = {$ldelim}'billing':'{$gift_cert_data.state|default:$settings.General.default_state|escape:"javascript"}'{$rdelim};
lang.no_products_defined = '{$lang.text_no_products_defined|escape:"javascript"}';
var max_amount = '{$addons.gift_certificates.max_amount|escape:javascript}';
var min_amount = '{$addons.gift_certificates.min_amount|escape:javascript}';
var amount_alert = '{$lang.text_gift_cert_amount_higher|escape:javascript} {$max_amount|escape:javascript} {$lang.text_gift_cert_amount_less|escape:javascript} {$min_amount|escape:javascript}';
//]]>
</script>

{** Gift certificates section **}

	{capture name="mainbox"}

	<form action="{""|fn_url}" method="post" target="_self" class="cm-form-highlight" name="gift_certificates_form" enctype="multipart/form-data">
	{if $mode == "update"}
	<input type="hidden" name="gift_cert_id" value="{$smarty.request.gift_cert_id}" />
	{/if}

	{** Page Section **}

	{if $mode == "update"}
	{capture name="tabsbox"}
	<div id="content_detailed" class="hidden">
	{/if}

	{** /Page Section **}

	{notes}
		{$lang.text_gift_cert_amount_higher}&nbsp;{include file="common_templates/price.tpl" value=$addons.gift_certificates.max_amount}&nbsp;{$lang.text_gift_cert_amount_less}&nbsp;{include file="common_templates/price.tpl" value=$addons.gift_certificates.min_amount}
	{/notes}

		{if $mode == "update"}
		<fieldset>
		<div class="form-field">
			<label for="gift_cert_code">{$lang.gift_cert_code}:</label>
			<input type="hidden" name="gift_cert_data[gift_cert_code]" id="gift_cert_code" value="{$gift_cert_data.gift_cert_code}" />
			<strong>{$gift_cert_data.gift_cert_code}</strong>
		</div>

		<div class="form-field">
			<label for="gift_cert_status">{$lang.status}:</label>
			<input type="hidden" name="certificate_status" value="{$gift_cert_data.status}" />
			{include file="common_templates/status.tpl" status=$gift_cert_data.status display="select" name="gift_cert_data[status]" status_type=$smarty.const.STATUSES_GIFT_CERTIFICATE select_id="gift_cert_status"}
		</div>
		{/if}

		<div class="form-field">
			<label for="gift_cert_recipient" class="cm-required">{$lang.gift_cert_to}:</label>
			<input type="text" id="gift_cert_recipient" name="gift_cert_data[recipient]"  class="input-text-large main-input" maxlength="255" value="{$gift_cert_data.recipient}" />
		</div>

		<div class="form-field">
			<label for="gift_cert_sender" class="cm-required">{$lang.gift_cert_from}:</label>
			<input type="text" id="gift_cert_sender" name="gift_cert_data[sender]" class="input-text-large" maxlength="255" value="{$gift_cert_data.sender}" />
		</div>

		<div class="form-field">
			<label for="gift_cert_message">{$lang.message}:</label>
			<textarea id="gift_cert_message" name="gift_cert_data[message]" cols="55" rows="6" class="input-textarea-long">{$gift_cert_data.message}</textarea>
			{include file="common_templates/wysiwyg.tpl" id="gift_cert_message"}
		</div>

		<div class="form-field">
			<label class="cm-required">{$lang.amount}:</label>
			<table cellpadding="1" cellspacing="1" border="0">
				<tr>
					<td width="5">
						<input type="radio" name="gift_cert_data[amount_type]" value="I" onclick="fn_giftcert_form_elements_disable('select_block', 'input_block');" {if $mode == "add" || $gift_cert_data.amount_type == "I"}checked="checked"{/if} {if !$amount_variants}class="hidden"{/if}/></td>
					<td>
						<div id="input_block">
						{if $currencies.$secondary_currency.after != "Y"}{$currencies.$secondary_currency.symbol}{/if}
						<input type="text" id="gift_cert_amount" name="gift_cert_data[amount]" class="input-text inp-el" size="5" value="{if $gift_cert_data && $gift_cert_data.amount_type == "I"}{$gift_cert_data.amount|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:".":"":$currencies.$secondary_currency.coefficient}{else}{$addons.gift_certificates.min_amount|fn_format_rate_value:"":$currencies.$secondary_currency.decimals:".":"":$currencies.$secondary_currency.coefficient}{/if}" />
						{if $currencies.$secondary_currency.after == "Y"}{$currencies.$secondary_currency.symbol}{/if}
						</div>
						</td>
					{if $amount_variants}
					<td width="5">
						<input type="radio" name="gift_cert_data[amount_type]" value="S" onclick="fn_giftcert_form_elements_disable('input_block', 'select_block');" {if $gift_cert_data.amount_type == "S"}checked="checked"{/if} /></td>
					<td>
							<div id="select_block">
							<select	id="gift_cert_amount2" name="gift_cert_data[amount]" class="input-text sel-el" >
							{foreach from=$amount_variants item="av"}
							{if $av == $gift_cert_data.amount}{assign var="av_isset" value="Y"}{/if}
							{if !$av_isset && $mode == "update" && $av > $gift_cert_data.amount}
								{assign var="av_isset" value="Y"}
								<option value="{$gift_cert_data.amount|fn_format_price}" {if $gift_cert_data.amount_type == "S"}selected="selected"{/if}>{include file="common_templates/price.tpl" value=$gift_cert_data.amount}</option>
								{/if}

								<option value="{$av|fn_format_price}" {if ($av == $gift_cert_data.amount && $gift_cert_data.amount_type == "S" && $gift_cert_data) || (!$gift_cert_data && $addons.gift_certificates.min_amount == $av)}selected="selected"{/if}>{include file="common_templates/price.tpl" value=$av}</option>
							{/foreach}
							</select></div></td>
					{/if}
				<tr>
			</table>
		</div>

		<div class="select-field">
			<input type="radio" name="gift_cert_data[send_via]" value="E" onclick="fn_giftcert_form_elements_disable('post_block', 'email_block');" {if $mode == "add" || $gift_cert_data.send_via == "E"}checked="checked"{/if} class="radio" />
			<label for="send_via">{$lang.send_via_email}</label>
		</div>

		<hr />

		<div id="email_block">
			<div class="form-field">
				<label for="gift_cert_email" class="cm-required cm-email">{$lang.email}:</label>
				<input type="text" id="gift_cert_email" name="gift_cert_data[email]" class="input-text-large" maxlength="128" value="{$gift_cert_data.email}" />
			</div>
		</div>

		<div class="select-field">
			<input type="radio" name="gift_cert_data[send_via]" value="P" onclick="fn_giftcert_form_elements_disable('email_block', 'post_block');" {if $gift_cert_data.send_via == "P"}checked="checked"{/if} class="radio" />
			<label for="gift_cert_send_via">{$lang.send_via_postal_mail}</label>
		</div>

		<hr />

		<div id="post_block">
			<div class="form-field">
				<label for="gift_cert_address" class="cm-required">{$lang.address}:</label>
				<input type="text" id="gift_cert_address" name="gift_cert_data[address]" class="input-text-large" value="{$gift_cert_data.address}"  />
			</div>

			<div class="form-field">
				<label for="gift_cert_address_2">{$lang.address_2}:</label>
				<input type="text" id="gift_cert_address_2" name="gift_cert_data[address_2]" class="input-text-large" value="{$gift_cert_data.address_2}" />
			</div>

			<div class="form-field">
				<label for="gift_cert_city" class="cm-required">{$lang.city}:</label>
				<input type="text" id="gift_cert_city" name="gift_cert_data[city]" class="input-text-large" value="{$gift_cert_data.city}" />
			</div>

			<div class="form-field">
				<label for="gift_cert_country" class="cm-required cm-country cm-location-billing">{$lang.country}:</label>
				{assign var="_country" value=$gift_cert_data.country|default:$settings.General.default_country}
				<select id="gift_cert_country" name="gift_cert_data[country]" class="input-text cm-location-billing">
					<option value="">- {$lang.select_country} -</option>
					{foreach from=$countries item=country}
						<option {if $_country == $country.code}selected="selected"{/if} value="{$country.code}">{$country.country}</option>
					{/foreach}
				</select>
			</div>

			<div class="form-field">
				<label for="gift_cert_state" class="cm-required cm-state cm-location-billing">{$lang.state}:</label>
				<input type="text" id="gift_cert_state_d" name="gift_cert_data[state]" class="input-text-medium hidden" maxlength="64" value="{$value}" disabled="disabled"  />
				<select id="gift_cert_state" name="gift_cert_data[state]"  class="input-text" >
					<option value="">- {$lang.select_state} -</option>
					{if $states}
						{foreach from=$states.$_country item=state}
							<option value="{$state.code}">{$state.state}</option>
						{/foreach}
					{/if}
				</select>
			</div>

			<div class="form-field">
				<label for="gift_cert_zipcode" class="cm-required">{$lang.zip_postal_code}:</label>
				<input type="text" id="gift_cert_zipcode" name="gift_cert_data[zipcode]" class="input-text-medium" value="{$gift_cert_data.zipcode}"  />
			</div>

			<div class="form-field">
				<label for="gift_cert_phone">{$lang.phone}:</label>
				<input type="text" id="gift_cert_phone" name="gift_cert_data[phone]" class="input-text-medium" value="{$gift_cert_data.phone}" />
			</div>
		</div>
		{if $mode == "update"}</fieldset>{/if}

		{if $addons.gift_certificates.free_products_allow == "Y"}
		{include file="common_templates/subheader.tpl" title=$lang.free_products}
		{include file="pickers/products_picker.tpl" data_id="free_products" item_ids=$gift_cert_data.products input_name="gift_cert_data[products]" type="table" holder_name="gift_certificates"}
		{/if}
		
		<div class="select-field notify-customer">
			<input type="checkbox" name="notify_user" id="notify_user" value="Y" class="checkbox" />
			<label for="notify_user">{$lang.notify_customer}</label>
		</div>

		<div class="buttons-container buttons-bg">
			{if $mode == "add"}
				{include file="buttons/save_cancel.tpl" but_name="dispatch[gift_certificates.add]" but_onclick="return fn_check_amount();" but_role="button_main"}
			{else}
				{capture name="tools_list"}
				<ul>
					<li><a name="dispatch[gift_certificates.preview]" class="cm-new-window" rev="gift_certificates_form">{$lang.preview}</a></li>
				</ul>
				{/capture}
			
				{capture name="gift_extra_tools"}
				{if $templates|@sizeof > 1}
					<select id="gift_cert_template" name="gift_cert_data[template]">
						{foreach from=$templates item="name" key="file"}
							<option value="{$file}">{$name}</option>
						{/foreach}
					</select>
				{else}
					{foreach from=$templates item="name" key="file"}
						<input id="gift_cert_template" type="hidden" name="gift_cert_data[template]" value="{$file}" />
					{/foreach}
				{/if}
				
				{include file="common_templates/tools.tpl" prefix="main" hide_actions=true tools_list=$smarty.capture.tools_list display="inline" link_text=$lang.choose_action}
				{/capture}
				{include file="buttons/save_cancel.tpl" but_name="dispatch[gift_certificates.update]" but_onclick="return fn_check_amount();" but_role="button_main" extra=$smarty.capture.gift_extra_tools}
			{/if}
		</div>

		</form>

	{** Page Section **}
	{if $mode == "update"}
		</div>
		<div id="content_log" class="hidden">
			{include file="common_templates/pagination.tpl"}

			{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

			{if $settings.DHTML.admin_ajax_based_pagination == "Y"}
				{assign var="ajax_class" value="cm-ajax"}
			{/if}

			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
			<tr>
				<th><a class="{$ajax_class}{if $sort_by == "timestamp"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=timestamp&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.date}</a></th>
				<th><a class="{$ajax_class}{if $sort_by == "email"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=email&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.email}</a></th>
				<th><a class="{$ajax_class}{if $sort_by == "name"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=name&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.name}</a></th>
				<th><a class="{$ajax_class}{if $sort_by == "order_id"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=order_id&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.order_id}</a></th>
				<th><a class="{$ajax_class}{if $sort_by == "amount"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=amount&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.balance}</a></th>
				<th><a class="{$ajax_class}{if $sort_by == "debit"} sort-link-{$sort_order}{/if}" href="{"`$c_url`&amp;sort_by=debit&amp;sort_order=`$sort_order`"|fn_url}" rev="pagination_contents">{$lang.gift_cert_debit}</a></th>
			</tr>
			{foreach from=$log item="l"}
			<tr {cycle values="class=\"table-row\", "}>
				<td>{$l.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
				<td class="nowrap">{if $l.user_id || $l.order_email}<a href="mailto:{if $l.user_id}{$l.email|escape:url}{else}{$l.order_email|escape:url}{/if}" class="underlined">{if $l.user_id}{$l.email}{else}{$l.order_email}{/if}</a>{else}-{/if}</td>
				<td class="nowrap">
					{if $l.user_id}
						<a href="{"profiles.update?user_id=`$l.user_id`"|fn_url}" class="underlined">{$l.firstname} {$l.lastname}</a>
					{elseif $l.order_id}
						{$l.order_firstname} {$l.order_lastname}
					{else}
						-
					{/if}
				</td>
				<td>{if $l.order_id}<a href="{"orders.details?order_id=`$l.order_id`&amp;selected_section=payment_information"|fn_url}" class="underlined">&nbsp;{$l.order_id}&nbsp;</a>{else}-{/if}</td>
				<td>
					{if $addons.gift_certificates.free_products_allow == "Y"}<strong>{$lang.amount}:</strong>&nbsp;{/if}{include file="common_templates/price.tpl" value=$l.amount}
					{if $l.products && $addons.gift_certificates.free_products_allow == "Y"}
					<p><strong>{$lang.free_products}:</strong></p>
					<ul>
					{foreach from=$l.products item="product_item"}
						<li>&nbsp;<strong>&#187;</strong>&nbsp;{$product_item.amount} - {if $product_item.product}<a href="{"products.update?product_id=`$product_item.product_id`"|fn_url}">{$product_item.product|truncate:30:"...":true}</a>{else}{$lang.deleted_product}{/if}</li>
					{/foreach}
					</ul>
					{/if}
				</td>
				<td>
					{if $addons.gift_certificates.free_products_allow == "Y"}<strong>{$lang.amount}:</strong>&nbsp;{/if}{include file="common_templates/price.tpl" value=$l.debit}
					{if $l.debit_products && $addons.gift_certificates.free_products_allow == "Y"}
					<p><strong>{$lang.free_products}:</strong></p>
					{foreach from=$l.debit_products item="product_item"}
					<div>
						&nbsp;<strong>&#187;</strong>&nbsp;{$product_item.amount} - {if $product_item.product}<a href="{"products.update?product_id=`$product_item.product_id`"|fn_url}">{$product_item.product|truncate:30:"...":true}</a>{else}{$lang.deleted_product}{/if}
					</div>
					{/foreach}
					{/if}
				</td>
			</tr>
			{foreachelse}
			<tr class="no-items">
				<td colspan="6"><p>{$lang.no_items}</p></td>
			</tr>
			{/foreach}
			</table>
			{include file="common_templates/pagination.tpl"}
		</div>
		{/capture}
		{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section}
	{/if}
	{** /Page Section **}

	<script type="text/javascript">
		//<![CDATA[
		fn_giftcert_form_elements_disable({if $mode == 'add' || $gift_cert_data.amount_type == 'I'}'select_block', 'input_block'{else}'input_block', 'select_block'{/if});
		fn_giftcert_form_elements_disable({if $mode == 'add' || $gift_cert_data.send_via == 'E'}'post_block', 'email_block'{else}'email_block', 'post_block'{/if});
		//]]>
	</script>

	{/capture}
	{if $mode == "add"}
		{assign var="title" value=$lang.new_certificate}
	{else}
		{assign var="title" value=$lang.editing_certificate|cat:": `$gift_cert_data.gift_cert_code`"}
		{include file="common_templates/view_tools.tpl" url="gift_certificates.update?gift_cert_id="}
	{/if}
	{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.view_tools}
{** / Gift certificates section **}
