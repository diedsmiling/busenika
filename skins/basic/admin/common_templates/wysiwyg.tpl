{* $Id: wysiwyg.tpl 7440 2009-05-06 13:32:20Z lexa $ *}

{if !$smarty.capture.wysiwyg}
<div class="script-holder"></div>
<script type="text/javascript">
//<![CDATA[
tiny_lang = '{$smarty.const.CART_LANGUAGE|lower}';

var node = document.createElement("script");
node.src = '{$config.current_path}/lib/tinymce/tiny_mce.js';
$('.script-holder').get(0).appendChild(node);
node = document.createElement("script");
node.src = '{$config.current_path}/lib/tinymce/tiny_mce_init.js';
$('.script-holder').get(0).appendChild(node);
node = null;
//]]>
</script>
{capture name="wysiwyg"}Y{/capture}
{/if}

<p>
{include file="buttons/button.tpl" but_text=$lang.edit_in_visual_editor but_onclick="jQuery.openEditor(this.id.str_replace('on_b', ''));" but_role="simple" but_meta="text-button cm-combination" but_id="on_b`$id`"}
{include file="buttons/button.tpl" but_text=$lang.view_source but_onclick="jQuery.openEditor(this.id.str_replace('off_b', ''));" but_role="simple" but_meta="text-button cm-combination hidden" but_id="off_b`$id`"}
</p>