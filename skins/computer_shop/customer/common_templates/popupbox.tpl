{* $Id: popupbox.tpl 9520 2010-05-19 14:50:42Z zeke $ *}

{if $capture_link}
	{capture name="link"}
{/if}

<a id="opener_{$id}" class="{if $link_meta}{$link_meta} {/if}{if $href}cm-ajax cm-ajax-cache{/if}"{if $href} href="{$href|fn_url}" rev="content_{$id}"{/if} onclick="{$edit_onclick} {if !$custom_opener}jQuery.show_picker('{$id}', '', '.object-container');{/if} return false;">{$link_text}</a>

{if $capture_link}
	{/capture}
{/if}

<div id="{$id}" class="popup{if !$edit_picker}-notes{/if}-content cm-popup-box cm-picker hidden">
	<div class="cm-popup-hor-resizer cm-left-resizer"></div>
	<div class="cm-popup-hor-resizer cm-right-resizer"></div>
	<div class="cm-popup-corner-resizer cm-nw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-ne-resizer"></div>
	<div class="cm-popup-corner-resizer cm-sw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-se-resizer"></div>
	<div class="cm-popup-vert-resizer cm-top-resizer"></div>
	<div class="cm-popup-content-header">
		<div class="popupbox-closer">
			<img src="{$images_dir}/icons/close_popupbox.png" border="0" alt="{$lang.close}" class="hand cm-popup-switch" />
		</div>
		<h3>{$text}:</h3>
	</div>

	<div class="cm-popup-content-footer" id="content_{$id}">{$content}</div>

	<div class="cm-popup-vert-resizer cm-bottom-resizer"></div>
</div>