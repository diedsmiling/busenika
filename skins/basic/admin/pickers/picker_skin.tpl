{* $Id: picker_skin.tpl 9769 2010-06-09 11:37:39Z lexa $ *}

<div class="popup-content cm-popup-box cm-picker hidden{if !$no_bg_close} cm-bg-close{/if}" id="picker_{$data_id}">
	<div class="cm-popup-hor-resizer cm-left-resizer"></div>
	<div class="cm-popup-hor-resizer cm-right-resizer"></div>
	<div class="cm-popup-corner-resizer cm-nw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-ne-resizer"></div>
	<div class="cm-popup-corner-resizer cm-sw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-se-resizer"></div>
	<div class="cm-popup-vert-resizer cm-top-resizer"></div>
	<div class="cm-popup-content-header">
		<div class="float-right">
			<img src="{$images_dir}/icons/icon_close.gif" width="13" height="13" border="0" alt="{$lang.close}" title="{$lang.close}" class="hand cm-popup-switch" />
		</div>
		<h3>{$but_text}:</h3>
	</div>
	<div class="cm-popup-content-footer">
		{$picker_content}
	</div>
	<div class="cm-popup-vert-resizer cm-bottom-resizer"></div>
</div>

