{* $Id: remove_item.tpl 5833 2008-09-02 07:48:35Z lexa $ *}

{if !$simple}
<img src="{$images_dir}/icons/icon_delete_disabled.gif" width="12" height="18" border="0" name="remove" id="{$item_id}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" class="hand{if $only_delete == "Y"} hidden{/if}" align="top" />
{/if}
<img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" name="remove_hidden" id="{$item_id}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}"{if $but_onclick} onclick="{$but_onclick}"{/if} class="hand{if !$simple && $only_delete != "Y"} hidden{/if}{if $but_class} {$but_class}{/if}" align="top" />
