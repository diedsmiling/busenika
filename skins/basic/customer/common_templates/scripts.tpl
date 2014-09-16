{* $Id: scripts.tpl 10199 2010-07-26 09:11:31Z klerik $ *}

{script src="lib/jquery/jquery.js"}
{script src="js/core.js"}
{script src="js/ajax.js"}
<script type="text/javascript">
//<![CDATA[
var index_script = '{$index_script|escape:"javascript"}';
var current_path = '{$config.current_path|escape:"javascript"}';
var changes_warning = '{$settings.Appearance.changes_warning|escape:"javascript"}';

var lang = {$ldelim}
	cannot_buy: '{$lang.cannot_buy|escape:"javascript"}',
	no_products_selected: '{$lang.no_products_selected|escape:"javascript"}',
	error_no_items_selected: '{$lang.error_no_items_selected|escape:"javascript"}',
	delete_confirmation: '{$lang.delete_confirmation|escape:"javascript"}',
	text_out_of_stock: '{$lang.text_out_of_stock|escape:javascript}',
	in_stock: '{$lang.in_stock|escape:javascript}',
	items: '{$lang.items|escape:javascript}',
	text_required_group_product: '{$lang.text_required_group_product|escape:javascript}',
	notice: '{$lang.notice|escape:"javascript"}',
	warning: '{$lang.warning|escape:"javascript"}',
	loading: '{$lang.loading|escape:"javascript"}',
	none: '{$lang.none|escape:"javascript"}',
	text_are_you_sure_to_proceed: '{$lang.text_are_you_sure_to_proceed|escape:"javascript"}',
	text_invalid_url: '{$lang.text_invalid_url|escape:"javascript"}',
	text_cart_changed: '{$lang.text_cart_changed|escape:"javascript"}',
	error_validator_email: '{$lang.error_validator_email|escape:"javascript"}',
	error_validator_confirm_email: '{$lang.error_validator_confirm_email|escape:"javascript"}',
	error_validator_phone: '{$lang.error_validator_phone|escape:"javascript"}',
	error_validator_integer: '{$lang.error_validator_integer|escape:"javascript"}',
	error_validator_multiple: '{$lang.error_validator_multiple|escape:"javascript"}',
	error_validator_password: '{$lang.error_validator_password|escape:"javascript"}',
	error_validator_required: '{$lang.error_validator_required|escape:"javascript"}',
	error_validator_zipcode: '{$lang.error_validator_zipcode|escape:"javascript"}',
	error_validator_message: '{$lang.error_validator_message|escape:"javascript"}',
	text_page_loading: '{$lang.text_page_loading|escape:"javascript"}',
	view_cart: '{$lang.view_cart|escape:javascript}',
	checkout: '{$lang.checkout|escape:javascript}',
	product_added_to_cart: '{$lang.product_added_to_cart|escape:javascript}',
	products_added_to_cart: '{$lang.products_added_to_cart|escape:javascript}',
	product_added_to_wl: '{$lang.product_added_to_wl|escape:javascript}',
	product_added_to_cl: '{$lang.product_added_to_cl|escape:javascript}',
	close: '{$lang.close|escape:javascript}',
	error: '{$lang.error|escape:"javascript"}',
	error_ajax: '{$lang.error_ajax|escape:"javascript"}',
	text_changes_not_saved: '{$lang.text_changes_not_saved|escape:"javascript"}',
	text_data_changed: '{$lang.text_data_changed|escape:"javascript"}'
{$rdelim}

var warning_mark = "&lt;&lt;";

var currencies = {$ldelim}
	'primary': {$ldelim}
		'decimals_separator': '{$currencies.$primary_currency.decimals_separator|escape:javascript}',
		'thousands_separator': '{$currencies.$primary_currency.thousands_separator|escape:javascript}',
		'decimals': '{$currencies.$primary_currency.decimals|escape:javascript}',
		'coefficient': '{$currencies.$primary_currency.coefficient|escape:javascript}'
	{$rdelim},
	'secondary': {$ldelim}
		'decimals_separator': '{$currencies.$secondary_currency.decimals_separator|escape:javascript}',
		'thousands_separator': '{$currencies.$secondary_currency.thousands_separator|escape:javascript}',
		'decimals': '{$currencies.$secondary_currency.decimals|escape:javascript}',
		'coefficient': '{$currencies.$secondary_currency.coefficient}'
	{$rdelim}
{$rdelim};

var cart_language = '{$smarty.const.CART_LANGUAGE}';
var images_dir = '{$images_dir}';
var notice_displaying_time = {if $settings.Appearance.notice_displaying_time}{$settings.Appearance.notice_displaying_time}{else}0{/if};
var cart_prices_w_taxes = {if ($settings.Appearance.cart_prices_w_taxes == 'Y' && 'CHECKOUT'|defined) || ($settings.Appearance.show_prices_taxed_clean == 'Y' && !'CHECKOUT'|defined)}true{else}false{/if};
var translate_mode = {if "TRANSLATION_MODE"|defined}true{else}false{/if};
var iframe_urls = new Array();
var iframe_extra = new Array();
var regexp = new Array();
$(document).ready(function(){$ldelim}
	jQuery.runCart('C');
{$rdelim});

document.write('<style>.cm-noscript {$ldelim} display:none {$rdelim}</style>'); // hide noscript tags
//]]>
</script>
{literal}
<!--[if lt IE 8]>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$('ul.dropdown li').hover(function(){
		$(this).addClass('hover');
		$('> .dir',this).addClass('open');
		$('ul:first',this).css('display', 'block');
	},function(){
		$(this).removeClass('hover');
		$('.open',this).removeClass('open');
		$('ul:first',this).css('display', 'none');
	});
});
//]]>
</script>
<![endif]-->
{/literal}
{hook name="index:scripts"}
{/hook}