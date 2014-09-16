{* $Id: remove_item.tpl 6020 2008-09-29 08:24:03Z zeke $ *}

{strip}
{if !$simple}
<img src="{$images_dir}/icons/remove_item_disabled.gif" width="14" height="15" border="0" name="remove" id="{$item_id}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" class="hand{if $only_delete == "Y"} hidden{/if}" align="top" />
{/if}
<img src="{$images_dir}/icons/remove_item.gif" width="14" height="15" border="0" name="remove_hidden" id="{$item_id}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}"{if $but_onclick} onclick="{$but_onclick}"{/if} class="hand{if !$simple && $only_delete != "Y"} hidden{/if}{if $but_class} {$but_class}{/if}" align="top" />
{/strip}