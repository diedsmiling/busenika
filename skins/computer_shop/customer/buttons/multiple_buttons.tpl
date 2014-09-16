{* $Id: multiple_buttons.tpl 7089 2009-03-19 13:40:38Z zeke $ *}

{script src="js/node_cloning.js"}

{assign var="tag_level" value=$tag_level|default:"1"}
{strip}
<span class="nowrap">&nbsp;&nbsp;{if !$hide_add}{include file="buttons/add_empty_item.tpl" but_onclick="$('#box_' + this.id).cloneNode($tag_level);" item_id=$item_id}&nbsp;{/if}

{if !$hide_clone}{include file="buttons/clone_item.tpl" but_onclick="$('#box_' + this.id).cloneNode($tag_level, true);" item_id=$item_id}&nbsp;{/if}

{include file="buttons/remove_item.tpl" item_id=$item_id but_class="cm-delete-row"}
&nbsp;</span>
{/strip}