{* $Id: translate_box.tpl 6354 2008-11-17 14:26:17Z lexa $ *}

<div id="translate_link" class="cm-popup-box hidden">
	<img src="{$images_dir}/icons/translate_icon.png" width="16" height="16" border="0" alt="{$lang.translate}" title="{$lang.translate}" onclick="fn_show_translate_box();" />
</div>
<div id="translate_box" class="cm-popup-box hidden">
	<div class="cm-popup-content-header">
		{assign var="icon_tpl" value="$images_dir/flags/%s.png"}
		<div class="float-right">
			{foreach from=$languages item=item key=id}
			<img src="{$id|string_format:$icon_tpl|lower}" width="16" height="16" border="0" alt="{$id}" title="{$item.name}" onclick="fn_switch_langvar(this);" class="icons{if $id == $smarty.const.CART_LANGUAGE} cm-cur-lang{/if}" />
			{/foreach}
		</div>
		{assign var="cart_lang" value=$smarty.const.CART_LANGUAGE}
		<h3 id="lang_header">{$languages.$cart_lang.name}:</h3>
	</div>
	<div class="cm-popup-content-footer">
		<input id="trans_val" class="input-text" type="text" value="" size="37" onkeyup="fn_change_phrase();"/>
		<div class="clear-both"></div>
		<span id="orig_phrase"></span>
		<div class="buttons-container">
			{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="fn_save_phrase();" but_text=$lang.save_translation cancel_action="close"}
		</div>
	</div>
</div>
{script src="js/design_mode.js"}
