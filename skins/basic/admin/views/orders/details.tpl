{* $Id: details.tpl 10592 2010-09-03 08:43:24Z 2tl $ *}

{capture name="mainbox"}

{capture name="extra_tools"}
	{if $status_settings.appearance_type == "C" && $order_info.doc_ids[$status_settings.appearance_type]}
		{assign var="print_order" value=$lang.print_credit_memo}
		{assign var="print_pdf_order" value=$lang.print_pdf_credit_memo}
	{elseif $status_settings.appearance_type == "O"}
		{assign var="print_order" value=$lang.print_order_details}
		{assign var="print_pdf_order" value=$lang.print_pdf_order_details}
	{else}
		{assign var="print_order" value=$lang.print_invoice}
		{assign var="print_pdf_order" value=$lang.print_pdf_invoice}
	{/if}
	<span id="order_extra_tools">
	{hook name="orders:details_tools"}
	{include file="buttons/button_popup.tpl" but_text=$print_order but_href="orders.print_invoice?order_id=`$order_info.order_id`" width="900" height="600" but_role="tool"}&nbsp;|&nbsp;
	{include file="buttons/button_popup.tpl" but_text=$print_pdf_order but_href="orders.print_deposit?order_id=`$order_info.order_id`" width="900" height="600" but_role="tool"}&nbsp;|&nbsp;
	{include file="buttons/button_popup.tpl" but_text=$lang.print_packing_slip but_href="orders.print_packing_slip?order_id=`$order_info.order_id`" width="900" height="600" but_role="tool"}&nbsp;|&nbsp;
	{include file="buttons/button.tpl" but_text=$lang.edit_order but_href="order_management.edit?order_id=`$order_info.order_id`" but_role="tool"}
	{/hook}
	<!--order_extra_tools--></span>
{/capture}

{capture name="tabsbox"}

{if $settings.General.use_shipments == "Y"}
	{script src="js/picker.js"}
	{capture name="add_new_picker"}
		{include file="views/shipments/components/new_shipment.tpl"}
	{/capture}
	{include file="common_templates/popupbox.tpl" id="add_shipment" content=$smarty.capture.add_new_picker text=$lang.new_shipment act="hidden"}
{/if}


<form action="{""|fn_url}" method="post" name="order_info_form" class="cm-form-highlight">
<input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
<input type="hidden" name="order_status" value="{$order_info.status}" />
<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

<div id="content_general">

	<div class="item-summary clear center" id="order_summary">
		<div class="float-right">
		{if $order_info.status == $smarty.const.STATUS_INCOMPLETED_ORDER}
			{assign var="get_additional_statuses" value=true}
		{else}
			{assign var="get_additional_statuses" value=false}
		{/if}
		{assign var="order_status_descr" value=$smarty.const.STATUSES_ORDER|fn_get_statuses:true:$get_additional_statuses:true}
		{assign var="extra_status" value=$config.current_url|escape:"url"}
		{if $order_info.have_suppliers == "Y"}
			{assign var="notify_supplier" value=true}
		{else}
			{assign var="notify_supplier" value=false}
		{/if}
		{include file="common_templates/select_popup.tpl" suffix="o" id=$order_info.order_id status=$order_info.status items_status=$order_status_descr update_controller="orders" notify=true notify_department=true notify_supplier=$notify_supplier status_rev="order_summary,order_extra_tools" extra="&return_url=`$extra_status`"}
		</div>
	
		<div class="float-left">
			
		{$lang.order}&nbsp;&nbsp;<span>#{$order_info.order_id}</span>&nbsp;
		{if $status_settings.appearance_type == "I" && $order_info.doc_ids[$status_settings.appearance_type]}
		
		{elseif $status_settings.appearance_type == "C" && $order_info.doc_ids[$status_settings.appearance_type]}
		({$lang.credit_memo}&nbsp;<span>#{$order_info.doc_ids[$status_settings.appearance_type]}</span>)&nbsp;
		{/if}
		{$lang.by}&nbsp;&nbsp;<strong>{if $order_info.user_id}<a href="{"profiles.update?user_id=`$order_info.user_id`"|fn_url}">{/if}{$order_info.firstname}&nbsp;{$order_info.lastname}{if $order_info.user_id}</a>{/if}</strong>&nbsp;
		{assign var="timestamp" value=$order_info.timestamp|date_format:"`$settings.Appearance.date_format`"|escape:url}
		{$lang.on}&nbsp;<a href="{"orders.manage?period=C&amp;time_from=`$timestamp`&amp;time_to=`$timestamp`"|fn_url}">{$order_info.timestamp|date_format:"`$settings.Appearance.date_format`"}</a>,&nbsp;&nbsp;{$order_info.timestamp|date_format:"`$settings.Appearance.time_format`"}
		</div>
		
		{hook name="orders:customer_shot_info"}
		{/hook}
	<!--order_summary--></div>
	
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr valign="top">
		<td width="68%">
			{* Customer info *}
			{include file="views/profiles/components/profiles_info.tpl" user_data=$order_info location="I"}
		</td>
		<td width="32%" class="details-block-container">
			{hook name="orders:payment_info"}
			{* Payment info *}
			{if $order_info.payment_id}
				{include file="common_templates/subheader.tpl" title=$lang.payment_information}
				<div class="form-field">
					<label>{$lang.method}:</label>
					{$order_info.payment_method.payment}{*&nbsp;{if $order_info.payment_method.description}({$order_info.payment_method.description}){/if}*}
				</div>

				{if $order_info.payment_info}
					{foreach from=$order_info.payment_info item=item key=key}
					{if $item && ($key != "expiry_year" && $key != "start_year")}
						<div class="form-field">
							<label>{if $key == "card"}{assign var="cc_exists" value=true}{$lang.credit_card}{elseif $key == "expiry_month"}{$lang.expiry_date}{elseif $key == "start_month"}{$lang.start_date}{else}{$lang.$key}{/if}:</label>
							{if $key == "order_status"}
								{include file="common_templates/status.tpl" status=$item display="view" status_type=""}
							{elseif $key == "reason_text"}
								{$item|nl2br}
							{elseif $key == "expiry_month"}
								{$item}/{$order_info.payment_info.expiry_year}
							{elseif $key == "start_month"}
								{$item}/{$order_info.payment_info.start_year}
							{else}
								{$item}
							{/if}
						</div>
					{/if}
					{/foreach}

					{if $cc_exists}
					<p class="right">
						<input type="hidden" name="order_ids[]" value="{$order_info.order_id}" />
						{include file="buttons/button.tpl" but_text=$lang.remove_cc_info but_name="dispatch[orders.remove_cc_info]"}
					</p>
					{/if}
				{/if}
			{/if}
			{/hook}

			{* Shipping info *}
			{if $order_info.shipping}
				{include file="common_templates/subheader.tpl" title=$lang.shipping_information}
			
				{foreach from=$order_info.shipping item="shipping" key="shipping_id" name="f_shipp"}
				<div class="form-field">
					<label>{$lang.method}:</label>
					{$shipping.shipping}
				</div>
				
				{if $settings.General.use_shipments != "Y"}
					<div class="form-field">
						<label for="tracking_number">{$lang.tracking_number}:</label>
						<input id="tracking_number" type="text" class="input-text-medium" name="update_shipping[{$shipping_id}][tracking_number]" size="45" value="{$shipping.tracking_number}" />
					</div>
					<div class="form-field">
						<label for="carrier_key">{$lang.carrier}:</label>
						{include file="common_templates/carriers.tpl" id="carrier_key" name="update_shipping[`$shipping_id`][carrier]" carrier=$shipping.carrier}
					</div>
				{/if}
				{foreachelse}
					{if $settings.General.use_shipments != "Y"}
						<div class="form-field">
							<label for="shipping_method">{$lang.method}:</label>
							{if $shippings}
								<select id="shipping_method" name="add_shipping[shipping_id]">
								{foreach from=$shippings item="shipping"}
									<option value="{$shipping.shipping_id}">{$shipping.shipping}</option>
								{/foreach}
								</select>
							{/if}
						</div>
					
						<div class="form-field">
							<label for="tracking_number">{$lang.tracking_number}:</label>
							<input id="tracking_number" type="text" class="input-text-medium" name="add_shipping[tracking_number]" size="45" />
						</div>
						<div class="form-field">
							<label for="carrier_key">{$lang.carrier}:</label>
							{include file="common_templates/carriers.tpl" id="carrier_key" name="add_shipping[carrier]"}
						</div>
					{/if}
				{/foreach}
			{/if}
			{if $settings.General.use_shipments == "Y"}
				<div class="form-field">
					{if $order_info.need_shipment}
						<div class="small-picker-container">{include file="common_templates/popupbox.tpl" id="add_shipment" content="" but_text=$lang.new_shipment act="create"}</div>
					{/if}
					<a href="{"shipments.manage?order_id=`$order_info.order_id`"|fn_url}">{$lang.view_shipments}&nbsp;({$order_info.shipment_ids|count})</a>
				</div>
			{/if}
			{include file="common_templates/subheader.tpl" title=$lang.date_to_delivery}
			<div class="form-field">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td>
						<label>Дата доставки:</label>
						{include file="common_templates/calendar.tpl" date_id="date_to_delivery" date_name="update_order[date_to_delivery]" date_val=$order_info.date_to_delivery|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
						</td>
						
					</tr>
					<tr>
					<td><p>10-14, 12-15, 14-18, 10-18, 18-20($)</p></td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td>
						<label>{$lang.delivery_to_time}:</label>
						<input type="text" name="update_order[time_to_delivery]" id="time_to_delivery" size="5" value="{$order_info.time_to_delivery}" class="input-text-small" />
						</td>
					</tr>
				</table>
			</div>			
		</td>
	</tr>
	</table>


	{* Products info *}
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th>{$lang.product}</th>
		<th width="5%">{$lang.price}</th>
		<th width="5%">{$lang.quantity}</th>
		{if $order_info.use_discount}
		<th width="5%">{$lang.discount}</th>
		{/if}
		{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
		<th width="5%">&nbsp;{$lang.tax}</th>
		{/if}
		<th width="7%" class="right">&nbsp;{$lang.subtotal}</th>
	</tr>
	{foreach from=$order_info.items item="oi" key="key"  name=loop}
	{hook name="orders:items_list_row"}
	{if !$oi.extra.parent}
	<tr {cycle values="class=\"table-row\", " name="class_cycle"}>
		<td> {$smarty.foreach.loop.iteration}
			{if !$oi.deleted_product}<a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{/if}{$oi.product|unescape}{if !$oi.deleted_product}</a>{/if}
			{hook name="orders:product_info"}
			{if $oi.product_code}<p>{$lang.sku}:&nbsp;{$oi.product_code}</p>{/if}
			{/hook}
			{if $oi.product_options}<div class="options-info">{include file="common_templates/options_info.tpl" product_options=$oi.product_options}</div>{/if}
		</td>
		<td class="nowrap">
			{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.original_price}{/if}</td>
		<td class="center">
			&nbsp;{$oi.amount}<br />
			{if $settings.General.use_shipments == "Y" && $oi.shipped_amount > 0}
				&nbsp;<span class="small-note">(<strong>{$oi.shipped_amount}</strong>&nbsp;{$lang.shipped})</span>
			{/if}
		</td>
		{if $order_info.use_discount}
		<td class="nowrap">
			{if $oi.extra.discount|floatval}{include file="common_templates/price.tpl" value=$oi.extra.discount}{else}-{/if}</td>
		{/if}
		{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
		<td class="nowrap">
			{if $oi.tax_value|floatval}{include file="common_templates/price.tpl" value=$oi.tax_value}{else}-{/if}</td>
		{/if}
		<td class="right">&nbsp;<strong>{if $oi.extra.exclude_from_calculate}{$lang.free}{else}{include file="common_templates/price.tpl" value=$oi.display_subtotal}{/if}</strong></td>
	</tr>
	{/if}
	{/hook}
	{/foreach}
	{hook name="orders:extra_list"}
	{/hook}
	</table>

	{* text_no_items_found*}

	<!--{***** Customer note, Staff note & Statistics *****}-->
	{hook name="orders:totals"}
	<div class="clear order-notes">
	<div class="float-left">
		<h3><label for="notes">{$lang.customer_notes}:</label></h3>
		<textarea class="input-textarea" name="update_order[notes]" id="notes" cols="40" rows="5">{$order_info.notes}</textarea>
	</div>
	
	<div class="float-left">
		<h3><label for="details">{$lang.staff_only_notes}:</label></h3>
		<textarea class="input-textarea" name="update_order[details]" id="details" cols="40" rows="5">{$order_info.details}</textarea>
	</div>

	<div class="float-right statistic-container">
		<ul class="statistic-list">
			<li>
				<em>{$lang.subtotal}:</em>
				<strong>{include file="common_templates/price.tpl" value=$order_info.display_subtotal}</strong>
			</li>

			{if $order_info.display_shipping_cost|floatval}
				<li>
					<em>{$lang.shipping_cost}:</em>
					<strong>{include file="common_templates/price.tpl" value=$order_info.display_shipping_cost}</strong>
				</li>
			{/if}

			{if $order_info.discount|floatval}
				<li>
					<em>{$lang.including_discount}:</em>
					<strong>{include file="common_templates/price.tpl" value=$order_info.discount}</strong>
				</li>
			{/if}

			{if $order_info.subtotal_discount|floatval}
			<li>
				<em>{$lang.order_discount}:</em>
				<strong>{include file="common_templates/price.tpl" value=$order_info.subtotal_discount}</strong>
			</li>
			{/if}

			{if $order_info.coupons}
			{foreach from=$order_info.coupons key="coupon" item="_c"}
				<li>
					<em>{$lang.discount_coupon}:</em>
					<strong>{$coupon}</strong>
				</li>
			{/foreach}
			{/if}

			{if $order_info.taxes}
				<li>
					<em>{$lang.taxes}:</em>
					<strong>&nbsp;</strong>
				</li>

				{foreach from=$order_info.taxes item="tax_data"}
				<li>
					<em>&nbsp;<strong>&middot;</strong>&nbsp;{$tax_data.description}&nbsp;{include file="common_templates/modifier.tpl" mod_value=$tax_data.rate_value mod_type=$tax_data.rate_type}{if $tax_data.price_includes_tax == "Y" && ($settings.Appearance.cart_prices_w_taxes != "Y" || $settings.General.tax_calculation == "subtotal")}&nbsp;{$lang.included}{/if}{if $tax_data.regnumber}&nbsp;({$tax_data.regnumber}){/if}</em>
					<strong>{include file="common_templates/price.tpl" value=$tax_data.tax_subtotal}</strong>
				</li>
				{/foreach}
			{/if}

			{if $order_info.tax_exempt == "Y"}
				<li>
					<em>{$lang.tax_exempt}</em>
					<strong>&nbsp;</strong>
				</li>
			{/if}

			{if $order_info.payment_surcharge|floatval}
				<li>
					<em>{$lang.payment_surcharge}:</em>
					<strong>{include file="common_templates/price.tpl" value=$order_info.payment_surcharge}</strong>
				</li>
			{/if}

			{hook name="orders:totals_content"}
			{/hook}

			<li class="total">
				<em>{$lang.total}:</em>
				<strong>{include file="common_templates/price.tpl" value=$order_info.total}</strong>
			</li>
		</ul>
	</div>
	</div>
	{/hook}
	<!--{***** /Customer note, Staff note & Statistics *****}-->
	
	{hook name="orders:staff_only_note"}
	{/hook}

<!--content_general--></div>

<div id="content_addons">

	{hook name="orders:customer_info"}
	{/hook}

<!--content_addons--></div>

{if $downloads_exist}
<div id="content_downloads">
	<input type="hidden" name="order_id" value="{$smarty.request.order_id}" />
	<input type="hidden" name="order_status" value="{$order_info.status}" />
	{foreach from=$order_info.items item="oi"}
	{if $oi.extra.is_edp == "Y"}
	<p><a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{$oi.product}</a></p>
		{if $oi.files}
		<input type="hidden" name="files_exists[]" value="{$oi.product_id}" />
		<table cellpadding="5" cellspacing="0" border="0" class="table">
		<tr>
			<th>{$lang.filename}</th>
			<th>{$lang.activation_mode}</th>
			<th>{$lang.downloads_max_left}</th>
			<th>{$lang.download_key_expiry}</th>
			<th>{$lang.active}</th>
		</tr>
		{foreach from=$oi.files item="file"}
		<tr>
			<td>{$file.file_name}</td>
			<td>
				{if $file.activation_type == "M"}{$lang.manually}</label>{elseif $file.activation_type == "I"}{$lang.immediately}{else}{$lang.after_full_payment}{/if}
			</td>
			<td>{if $file.max_downloads}{$file.max_downloads} / <input type="text" class="input-text-short" name="edp_downloads[{$file.ekey}][{$file.file_id}]" value="{math equation="a-b" a=$file.max_downloads b=$file.downloads|default:0}" size="3" />{else}{$lang.none}{/if}</td>
			<td>
				{if $file.ekey}
				<p><label>{$lang.download_key_expiry}: </label><strong>{$file.ttl|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"|default:"n/a"}</strong></p>
				
				<p><label>{$lang.prolongate_download_key}: </label>{include file="common_templates/calendar.tpl" date_id="prolongate_date_`$file.file_id`" date_name="prolongate_data[`$file.ekey`]" date_val=$file.ttl|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}</p>
				{else}{$lang.file_doesnt_have_key}{/if}
			</td>
			<td>
				<select name="activate_files[{$oi.product_id}][{$file.file_id}]">
					<option value="Y" {if $file.active == "Y"}selected="selected"{/if}>{$lang.active}</option>
					<option value="N" {if $file.active == "N"}selected="selected"{/if}>{$lang.not_active}</option>
				</select>
			</td>
		</tr>
		{/foreach}
		</table>
		{/if}
	{/if}
	{/foreach}
<!--content_downloads--></div>
{/if}

{if $order_info.promotions}
<div id="content_promotions">
	{include file="views/orders/components/promotions.tpl" promotions=$order_info.promotions}
<!--content_promotions--></div>
{/if}

{hook name="orders:tabs_content"}
{/hook}

<div class="cm-toggle-button">
	<div class="select-field notify-customer">
		<input type="checkbox" name="notify_user" id="notify_user" value="N" class="checkbox" checked="checked" />
		<label for="notify_user">{$lang.notify_customer}</label>
	</div>

	<div class="select-field notify-department">
		<input type="checkbox" name="notify_department" id="notify_department" value="N" class="checkbox" />
		<label for="notify_department">{$lang.notify_orders_department}</label>
	</div>

{if $order_info.have_suppliers == "Y"}
	<div class="select-field notify-department">
		<input type="checkbox" name="notify_supplier" id="notify_supplier" value="Y" class="checkbox" />
		<label for="notify_supplier">{$lang.notify_supplier}</label>
	</div>
{/if}

	<div class="buttons-container buttons-bg">
		{include file="buttons/save_cancel.tpl" but_name="dispatch[orders.update_details]"}
	</div>
</div>
</form>

{if $google_info}
<div class="cm-hide-save-button" id="content_google">
	{include file="views/orders/components/google_actions.tpl"}
<!--content_google--></div>
{/if}

{hook name="orders:tabs_extra"}
{/hook}

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

{/capture}
{capture name="mainbox_title"}
	{$lang.viewing_order} #{$order_info.order_id} <span class="total">( {$lang.total}: <strong>{include file="common_templates/price.tpl" value=$order_info.total}</strong> )</span>
{/capture}

{include file="common_templates/view_tools.tpl" url="orders.details?order_id="}

{include file="common_templates/mainbox.tpl" title=$smarty.capture.mainbox_title content=$smarty.capture.mainbox tools=$smarty.capture.view_tools extra_tools=$smarty.capture.extra_tools}
