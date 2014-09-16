{* $Id: clone_delete.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{if $href_clone}
<a class="clone-item" href="{$href_clone|fn_url}"><img src="{$images_dir}/icons/icon_clone.gif" width="13" height="18" border="0" alt="{$lang.clone_this_item|escape:html}" title="{$lang.clone_this_item|escape:html}" /></a>
{/if}
<a class="delete-item {if !$no_confirm}cm-confirm{/if}{if $microformats} {$microformats}{/if}" {if $href_delete}href="{$href_delete|fn_url}"{/if} {if $rev_delete}rev="{$rev_delete}"{/if}><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" alt="{$lang.delete|escape:html}" title="{$lang.delete|escape:html}" /></a>