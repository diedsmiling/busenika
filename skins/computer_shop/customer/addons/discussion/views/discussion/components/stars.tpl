{* $Id: stars.tpl 9910 2010-06-30 08:22:42Z angel $ *}

<p class="nowrap stars">
{if $controller == "products" && $mode == "view"}<a onclick="$('#block_discussion').click(); return false;">{/if}
{section name="full_star" loop=$stars.full}<img src="{$images_dir}/icons/star_full.gif" width="13" height="12" alt="*" />{/section}
{if $stars.part}<img src="{$images_dir}/icons/star_{$stars.part}.gif" width="13" height="12" alt="" />{/if}
{section name="full_star" loop=$stars.empty}<img src="{$images_dir}/icons/star_empty.gif" width="13" height="12" alt="" />{/section}
{if $controller == "products" && $mode == "view"}</a>{/if}
</p>