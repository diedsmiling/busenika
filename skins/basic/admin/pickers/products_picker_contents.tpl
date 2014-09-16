{* $Id: products_picker_contents.tpl 9517 2010-05-19 14:02:43Z klerik $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>{$lang.products}</title>
{include file="common_templates/styles.tpl"}
{include file="common_templates/scripts.tpl"}
{if !$smarty.request.extra}
<script type="text/javascript">
//<![CDATA[
lang.text_items_added = '{$lang.text_items_added|escape:javascript}';
lang.options = '{$lang.options|escape:"javascript"}';

{if $smarty.request.display == "options" || $smarty.request.display == "options_amount" || $smarty.request.display == "options_price"}
	lang.no = '{$lang.no|escape:"javascript"}';
	lang.yes = '{$lang.yes|escape:"javascript"}';
	lang.aoc = '{$lang.any_option_combinations|escape:"javascript"}';

{literal}
	var options_routine = {
		disable: function (id, obj) {
			if (obj.checked) {
				$('*:input:not(#' + obj.id + ')', $('#' + id)).attr('disabled', 'disabled');
			} else {
				$('*:input:not(#' + obj.id + ')', $('#' + id)).removeAttr('disabled');
			}
		},
		get_description: function (obj, id) {
			var p = {};
			var d = '';
			var aoc = $('#option_' + id + '_AOC').get(0);
			if (aoc && aoc.checked) {
				d = lang.aoc;
			} else {
				$(':input', $('#opt_' + id)).each( function() {
					var op = this;
					var j_op = $(this);
					
					if (typeof(op.name) == 'string' && op.name == '') {
						return false;
					}

					var option_id = op.name.match(/\[(\d+)\]$/)[1];
					if (op.type == 'checkbox') {
						var variant = (op.checked == false) ? lang.no : lang.yes;
					}
					if (op.type == 'radio' && op.checked == true) {
						var variant = $('#option_description_' + id + '_' + option_id + '_' + op.value).text();
					}
					if (op.type == 'select-one') {
						var variant = op.options[op.selectedIndex].text;
					}
					if ((op.type == 'text' || op.type == 'textarea') && op.value != '') {
						if (j_op.hasClass('cm-hint') && op.value == op.defaultValue) { //FIXME: We should not become attached to cm-hint class
							var variant = '';
						} else {
							var variant = op.value;
						}
					}
					if ((op.type == 'checkbox') || ((op.type == 'text' || op.type == 'textarea') && op.value != '') || (op.type == 'select-one') || (op.type == 'radio' && op.checked == true)) {
						if (op.type == 'checkbox') {
							p[option_id] = (op.checked == false) ? $('#unchecked_' + id + '_option_' + option_id).val() : op.value;
						}else{
							p[option_id] = (j_op.hasClass('cm-hint') && op.value == op.defaultValue) ? '' : op.value; //FIXME: We should not become attached to cm-hint class
						}

						d += (d ? ',  ' : '') + $('#option_description_' + id + '_' + option_id).text() + variant;
					}
				});
			}
			return {path: p, desc: d != '' ? '<strong>' + lang.options + ':  </strong>' + d : ''};
		}

	}
{/literal}
{/if}
{literal}
	function fn_transfer_js_products(hide, close)
	{
		var d_form = document.forms['add_products'];
		
		if(!d_form){
			return false;
		}
		var products = {};
		var message = '';

		if ($('input.cm-item:checked', $(d_form)).length > 0) {
			if (!close) {
				$('input.cm-item:checked', $(d_form)).each( function() {
					var id = $(this).val();
					{/literal}
					{if $smarty.request.display == "options" || $smarty.request.display == "options_amount" || $smarty.request.display == "options_price"}
						{literal}
							var option = options_routine.get_description(d_form, id);
							var options_combination = id;
							
							for(var ind in option.path) {
								options_combination += "_" + ind + "_" + option.path[ind];
							}
							
							products[id] = {};
							products[id].option = option;
							products[id].value = $('#product_' + id).val();
						{/literal}
					{else}
						products[id] = $('#product_' + id).val();
					{/if}
					{literal}
				});
				
				fn_set_hook('transfer_js_items', products);
				
				parent.window.jQuery.add_js_item(products, 'p', message, hide);
			}

			jQuery.showNotifications({'notification': {'type': 'N', 'title': lang.notice, 'message': lang.text_items_added, 'save_state': false}});
		}
	}
	
	function fn_form_submit_post_add_products(frm, elm) {
		var close = (elm.attr('id') == 'add_item_close') ? true : false;
		
		fn_transfer_js_products(close, false);
		
		return false;
	}
{/literal}
//]]>
</script>
{/if}
</head>

<body class="picker-body">
{**[LOADING_MESSAGE]**}
{include file="common_templates/loading_box.tpl"}
{**[/LOADING_MESSAGE]**}

<div class="hidden">{include file="common_templates/notification.tpl"}</div>
{include file="views/products/components/products_search_form.tpl" dispatch="products.picker" extra="<input type=\"hidden\" name=\"display\" value=\"`$smarty.request.display`\" /><input type=\"hidden\" name=\"extra\" value=\"`$smarty.request.extra`\" /><input type=\"hidden\" name=\"checkbox_name\" value=\"`$smarty.request.checkbox_name`\" /><input type=\"hidden\" name=\"aoc\" value=\"`$smarty.request.aoc`\" />"}

{if $products}
{if $smarty.request.extra}
    {assign var="_extra" value="?`$smarty.request.extra`"}
{/if}
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="add_products" class="cm-js-post" enctype="multipart/form-data">
{/if}

{if $smarty.request.display != "options_amount" && $smarty.request.display != "options_price"}
	{assign var="hide_amount" value=true}
{/if}

{if $smarty.request.display == "options_price"}
	{assign var="show_price" value=true}
{/if}

{include file="views/products/components/products_list.tpl" products=$products form_name="add_products" checkbox_id="add_product_checkbox" pagination_suffix="#add_products" hide_amount=$hide_amount show_price=$show_price checkbox_name=$smarty.request.checkbox_name show_aoc=$smarty.request.aoc additional_class="option-item"}

{if $products}
<div class="buttons-container hidden">
	{if $smarty.request.extra}
		{if $hide_amount}
			{assign var="submit_meta" value="cm-parent-window cm-process-items"}
		{else}
			{assign var="submit_meta" value="cm-parent-window"}
		{/if}
		{include file="buttons/add_products.tpl" but_id="add_item" but_meta=$submit_meta but_name="submit" but_role="button_main"}
	{else}
		{include file="buttons/add_products.tpl" but_id="add_item" but_name="submit" but_role="button_main" but_meta="cm-process-items"}
		{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_text=$lang.add_products_and_close but_role="action" but_meta="cm-process-items"}
	{/if}
</div>

</form>
{/if}

{if "TRANSLATION_MODE"|defined}
	{include file="common_templates/translate_box.tpl"}
{/if}
</body>

</html>
