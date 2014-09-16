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
lang.text_items_added = '{$lang.text_items_added|escape:"javascript"}';
lang.options = '{$lang.options|escape:"javascript"}';
lang.exception = '{$lang.isset_js_product_exception|escape:"javascript"}';
var exception_combinations = {$ldelim}{$rdelim};
{if $smarty.request.display == "options" || $smarty.request.display == "options_amount" || $smarty.request.display == "options_price"}
	lang.no = '{$lang.no|escape:"javascript"}';
	lang.yes = '{$lang.yes|escape:"javascript"}';
	lang.aoc = '{$lang.any_option_combinations|escape:"javascript"}';

	{if $products}
		{foreach from=$products item="p"}
			{if $p.exception_combinations}
				{foreach from=$p.exception_combinations item="ec" key="ex_comb"}
					exception_combinations['{$p.product_id}_{$ex_comb}'] = true;
				{/foreach}
			{/if}
		{/foreach}
	{/if}

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
					var option_id = 0;
					
					if (op.name != 'undefined' && op.name.length > 0) {
						option_id = op.name.match(/\[(\d+)\]$/)[1];
					}
					
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
							p[option_id] = (op.checked == false) ? $('#unchecked_option_' + id + '_' + option_id).val() : op.value;
						}else{
							p[option_id] = (j_op.hasClass('cm-hint') && op.value == op.defaultValue) ? '' : op.value; //FIXME: We should not become attached to cm-hint class
						}
						d += (d ? ',  ' : '') + $('label[for=option_' + id + '_' + option_id + ']').text().replace(/\s?\(\?\)/, '') + variant;
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
		var d_form = document.forms['free_products_form'];
		if(!d_form){
			return false;
		}
		var products = {};
		var exception_message = '';
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
					if (!exception_combinations[options_combination]){
						products[id] = {};
						products[id].option = option;
						products[id].value = $('#product_' + id).val();
					} else {
						exception_message += "\n   -" + $('#product_' + id).val();
						exception_message += "\n        " + (option.desc != "" ? lang.options + ": " + option.desc : "");
					}
					{/literal}
					{else}
					products[id] = $('#product_' + id).val();
					{/if}
					{literal}
				});
				if (exception_message != '') {
					message += lang.exception.str_replace("[products]", exception_message + "\n");
				}
				parent.window.jQuery.add_js_item(products, 'p', message, hide);
			}

			jQuery.showNotifications({'notification': {'type': 'N', 'title': lang.notice, 'message': lang.text_items_added, 'save_state': false}});
		}
	}
	
	function fn_form_submit_post_free_products_form(frm, elm)
	{
		var close = (elm.attr('id') == 'add_item_close') ? true : false;
		
		fn_transfer_js_products(close, false);
		
		return false;
	}
{/literal}
//]]>
</script>
{assign var="js_product_var" value=true}
{/if}
</head>
{assign var="id" value=$lang.search_for_products|md5|string_format:"s_%s"}
<body {if $smarty.request.forced_closure}onload="$('#{$id}').switchBox(true);"{/if} class="clear-body">
{**[LOADING_MESSAGE]**}
{include file="common_templates/loading_box.tpl"}
{**[/LOADING_MESSAGE]**}

{assign var="search_extra" value="<input type=\"hidden\" name=\"display\" value=\"`$smarty.request.display`\" /><input type=\"hidden\" name=\"extra\" value=\"`$smarty.request.extra`\" />"}
{include file="views/products/components/products_search_form.tpl" search_extra=$search_extra dispatch="products.picker" simple_search_form=true}

{if $products}
{if $smarty.request.extra}
    {assign var="_extra" value="?`$smarty.request.extra`"}
{/if}
<form action="{"`$index_script``$_extra`"|fn_url}" method="post" name="free_products_form" class="cm-js-post" enctype="multipart/form-data">
{/if}

{include file="blocks/list_templates/products_list.tpl" 
show_name=true 
show_sku=true 
show_rating=true 
show_features=true 
show_prod_descr=true 
show_old_price=true 
show_price=true 
show_clean_price=true 
show_list_discount=true 
show_discount_label=true 
show_product_amount=true 
show_product_options=true 
show_qty=true 
show_min_qty=true 
show_product_edp=true 
show_descr=true 
disable_ids="bulk_addition_" 
dont_show_points=true 
bulk_addition=true 
js_product_var=$js_product_var 
hide_form=true 
bulk_add=true 
hide_layouts=true 
hide_links=true
}

{if $products}
<div class="buttons-container hidden">
	{if $smarty.request.extra}
		{include file="buttons/add_products.tpl" but_id="add_item" but_meta="cm-parent-window cm-process-items" but_name="submit" but_role="submit"}
	{else}
		{include file="buttons/button.tpl" but_id="add_item_close" but_name="submit" but_text=$lang.add_products_and_close but_role="action" but_meta="cm-process-items"}
		{include file="buttons/add_products.tpl" but_id="add_item" but_name="submit" but_role="submit" but_meta="cm-process-items"}
	{/if}
</div>
</form>
{else}
	<p class="no-items">{$lang.text_no_matching_results_found}</p>
{/if}

</body>
</html>