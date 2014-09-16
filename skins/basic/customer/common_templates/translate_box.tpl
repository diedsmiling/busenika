{* $Id: translate_box.tpl 6483 2008-12-03 14:57:53Z zeke $ *}

<div id="translate_link" class="cm-popup-box hidden">
	<a class="edit-link" onclick="fn_show_translate_box();">{$lang.edit}</a>
</div>
<div id="translate_box" class="cm-popup-box hidden">
	<div class="cm-popup-content-header">
		{assign var="icon_tpl" value="`$images_dir`/flags/%s.png"}
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
			<span class="submit-button cm-button-main">
			<input type="button" onclick="fn_save_phrase();" value="{$lang.save_translation}" />
			</span>
			&nbsp;&nbsp;&nbsp;{$lang.or}&nbsp;&nbsp;&nbsp;
			<a class="cm-popup-switch">{$lang.cancel}</a>
		</div>
	</div>
</div>

{script src="js/jquery.easydrag.js"}
{script src="js/design_mode.js"}
