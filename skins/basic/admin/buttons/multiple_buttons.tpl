{* $Id: multiple_buttons.tpl 7165 2009-03-31 12:38:30Z angel $ *}

{script src="js/node_cloning.js"}

{assign var="tag_level" value=$tag_level|default:"1"}
{strip}
{if $only_delete != "Y"}
<span class="nowrap">{if !$hide_add}{include file="buttons/add_empty_item.tpl" but_onclick="$('#box_' + this.id).cloneNode($tag_level); `$on_add`" item_id=$item_id}&nbsp;{/if}

{if !$hide_clone}{include file="buttons/clone_item.tpl" but_onclick="$('#box_' + this.id).cloneNode($tag_level, true);" item_id=$item_id}&nbsp;{/if}
{/if}
{include file="buttons/remove_item.tpl" only_delete=$only_delete but_class="cm-delete-row"}

&nbsp;</span>
{/strip}