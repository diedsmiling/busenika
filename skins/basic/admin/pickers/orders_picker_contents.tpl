{* $Id: orders_picker_contents.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>{$lang.categories}</title>
{include file="common_templates/styles.tpl"}
{include file="common_templates/scripts.tpl"}

<script type="text/javascript">
//<![CDATA[
lang.text_items_added = '{$lang.text_items_added|escape:javascript}';
{if !$smarty.request.extra}
{literal}
	function fn_add_js_orders(hide, close)
	{
		var d_form = document.forms['add_orders'];
		if(!d_form){
			return false;
		}
		var orders = {};

		if ($('input.cm-item:checked', $(d_form)).length > 0) {
			if (!close) {
				$('input.cm-item:checked', $(d_form)).each( function() {
					var id = $(this).val();
					var item = $(this).parent().parent();
					orders[id] = {'status': item.find('td.cm-order-status').text(), 'customer': item.find('td.cm-order-customer').text(), 'timestamp': item.find('td.cm-order-timestamp').text(), 'total': item.find('td.cm-order-total').text()};
				});
				parent.window.jQuery.add_js_item(orders, 'o', null, hide);
			}

			jQuery.showNotifications({'notification': {'type': 'N', 'title': lang.notice, 'message': lang.text_items_added, 'save_state': false}});
		}
	}
{/literal}
{/if}

var trg = '';
//]]>
</script>

</head>

<body class="picker-body">
{**[LOADING_MESSAGE]**}
{include file="common_templates/loading_box.tpl"}
{**[/LOADING_MESSAGE]**}

<div class="hidden">{include file="common_templates/notification.tpl"}</div>

{include file="views/orders/components/orders_search_form.tpl" dispatch="orders.picker" report_id=$report_id table_id=$table.table_id selected_section="order" extra="<input type=\"hidden\" name=\"extra\" value=\"`$smarty.request.extra`\" />"}
{if $smarty.request.extra}
    {assign var="_extra" value="?`$smarty.request.extra`"}
{/if}
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="add_orders">

{include file="common_templates/pagination.tpl" save_current_page=true}

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table">
<tr>
	<th class="center" width="1%">
		<input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
	<th width="10%">{$lang.id}</th>
	<th width="15%">{$lang.status}</th>
	<th width="25%">{$lang.customer}</th>
	<th width="25%">{$lang.date}</th>
	<th width="24%" class="right">{$lang.total}</th>
</tr>
{foreach from=$orders item="o"}
<tr {cycle values="class=\"table-row\", "}>
	<td class="center" width="1%">
		<input type="checkbox" name="add_parameter[]" value="{$o.order_id}" class="checkbox cm-item" /></td>
	<td>
		<strong>#{$o.order_id}</strong></td>
	<td class="cm-order-status"><input type="hidden" name="origin_statuses[{$o.order_id}]" value="{$o.status}" />{include file="common_templates/status.tpl" status=$o.status display="view" name="order_statuses[`$o.order_id`]"}</td>
	<td class="cm-order-customer">{$o.firstname} {$o.lastname}</td>
	<td class="cm-order-timestamp">
		{$o.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</td>
	<td class="right cm-order-total">
		{include file="common_templates/price.tpl" value=$o.total}</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="6"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>

{include file="common_templates/pagination.tpl"}

<div class="buttons-container hidden">
{if $smarty.request.extra}
	{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_orders but_meta="cm-parent-window cm-process-items" but_name="submit" but_role="button_main"}
{else}
	{include file="buttons/button.tpl" but_id="add_item" but_text=$lang.add_orders but_name="submit" but_onclick="fn_add_js_orders(false, false);" but_role="button_main" but_meta="cm-process-items cm-no-submit"}
	{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_text=$lang.add_orders_and_close but_onclick="fn_add_js_orders(true, false);" but_role="action" but_meta="cm-process-items cm-no-submit"}
{/if}
</div>
</form>

{if "TRANSLATION_MODE"|defined}
	{include file="common_templates/translate_box.tpl"}
{/if}
</body>

</html>
