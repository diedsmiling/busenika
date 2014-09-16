{* $Id: product_banner_update.tpl 10380 2010-08-09 05:57:45Z klerik $ *}

{script src="js/picker.js"}
<script type="text/javascript">
//<![CDATA[
function fn_get_example_banner()
{ldelim}
	jQuery.ajaxRequest('{"aff_banner.view"|fn_url:'A':'rel':'&'}' +
		'&image=' + $('#id_banner_image').val() +
		'&product_name=' + $('#id_banner_product_name').val()+
		'&short_description=' + $('#id_banner_short_description').val()+
		'&width=' + $('#id_banner_width').val()+
		'&height=' + $('#id_banner_height').val()+
		'&align=' + $('#id_banner_align').val()+
		'&new_window=' + $('#id_banner_new_window').val()+
		'&border=' + $('#id_banner_border').val()+
		'&to_cart=' + $('#id_banner_to_cart').val()+
		'&type=' + $('#id_banner_type').val(), {ldelim}result_ids: 'id_example_banner', force_exec: true{rdelim}
	);
{rdelim}
//]]>
</script>

<div class="float-right">
	{capture name="preview_banner_content"}
		<div class="object-container">
			<div id="id_example_banner">{if $banner.code}{$banner.code|unescape}{/if}</div>
			<input id="id_banner_type" type="hidden" value="js" />
			<p><a onclick="fn_get_example_banner(); return false;">{$lang.refresh_banner}</a></p>
		</div>
	{/capture}
	{include file="common_templates/popupbox.tpl" content=$smarty.capture.preview_banner_content act="notes" id="preview_banner" text=$lang.preview link_text=$lang.preview}
</div>

<form action="{""|fn_url}" method="post" name="product_banners_addition_form" class="cm-form-highlight">
<input type="hidden" name="banner_id" value="{$banner.banner_id}" />
<input type="hidden" name="banner[type]" value="P" />
<input type="hidden" name="banner[link_to]" value="{$link_to}" />

<fieldset>

<div class="form-field">
	<label for="title" class="cm-required">{$lang.title}:</label>
	<input type="text" name="banner[title]" id="title" value="{$banner.title}" size="50" class="input-text-large main-input" /><input type="hidden" name="banner[show_title]" value="N" />
</div>

<div class="form-field">
	<label for="id_banner_width">{$lang.width}&nbsp;({$lang.pixels}):</label>
	<input type="text" id="id_banner_width" name="banner[width]" value="{$banner.width}" size="10" class="input-text" />
</div>

<div class="form-field">
	<label for="id_banner_height">{$lang.height}&nbsp;({$lang.pixels}):</label>
	<input type="text" id="id_banner_height" name="banner[height]" value="{$banner.height}" size="10" class="input-text" />
</div>

<div class="form-field">
	<label for="id_banner_image">{$lang.image}:</label>
	<select name="banner[data][image]" id="id_banner_image">
		<option value="N" {if $banner.image == "N"}selected="selected"{/if}>{$lang.not_show}</option>
		<option value="T" {if $banner.image == "T" || !$banner}selected="selected"{/if}>{$lang.top}</option>
		<option value="B" {if $banner.image == "B"}selected="selected"{/if}>{$lang.bottom}</option>
	</select>
</div>

<div class="form-field">
	<label for="id_banner_product_name">{$lang.product_name}:</label>
	<select name="banner[data][product_name]" id="id_banner_product_name">
		<option value="N" {if $banner.product_name == "N"}selected="selected"{/if}>{$lang.not_show}</option>
		<option value="T" {if $banner.product_name == "T" || !$banner}selected="selected"{/if}>{$lang.top}</option>
		<option value="B" {if $banner.product_name == "B"}selected="selected"{/if}>{$lang.bottom}</option>
	</select>
</div>

<div class="form-field">
	<label for="id_banner_short_description">{$lang.short_description}:</label>
	<select name="banner[data][short_description]" id="id_banner_short_description">
		<option value="N" {if $banner.short_description == "N"}selected="selected"{/if}>{$lang.not_show}</option>
		<option value="T" {if $banner.short_description == "T" || !$banner}selected="selected"{/if}>{$lang.top}</option>
		<option value="B" {if $banner.short_description == "B"}selected="selected"{/if}>{$lang.bottom}</option>
	</select>
</div>

<div class="form-field">
	<label for="id_banner_align">{$lang.align}:</label>
	<select name="banner[data][align]" id="id_banner_align">
		<option value="center" {if $banner.align == "center" || !$banner}selected="selected"{/if}>{$lang.center}</option>
		<option value="left" {if $banner.align == "left"}selected="selected"{/if}>{$lang.left}</option>
		<option value="right" {if $banner.align == "right"}selected="selected"{/if}>{$lang.right}</option>
	</select>
</div>

<div class="form-field">
	<label for="id_banner_border">{$lang.show_border}:</label>
	<input type="hidden" name="banner[data][border]" value="N" />
	<input type="checkbox" id="id_banner_border" name="banner[data][border]" {if $banner.border == "Y"}checked="checked"{/if} value="Y" class="checkbox" />
</div>

<div class="form-field">
	<label for="id_banner_to_cart">{$lang.add_to_cart}:</label>
	<input type="hidden" name="banner[to_cart]" value="N" />
	<input type="checkbox" id="id_banner_to_cart" name="banner[to_cart]" {if $banner.to_cart == "Y"}checked="checked"{/if} value="Y" class="checkbox" />
</div>

<div class="form-field">
	<label for="id_banner_new_window">{$lang.open_in_new_window}:</label>
	<input type="hidden" name="banner[new_window]" value="N" />
	<input type="checkbox" id="id_banner_new_window" name="banner[new_window]" {if $banner.new_window == "Y"}checked="checked"{/if} value="Y" class="checkbox" />
</div>

{include file="common_templates/select_status.tpl" input_name="banner[status]" id="banner" obj=$banner}
</fieldset>

<div class="buttons-container buttons-bg">
	{include file="buttons/save_cancel.tpl" but_name="dispatch[banners_manager.update]"}
</div>

</form>
