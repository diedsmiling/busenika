{* $Id: stars.tpl 6888 2009-02-06 14:39:39Z angel $ *}

{section name="full_star" loop=$stars.full}<img src="{$images_dir}/icons/star_full.gif" width="16" height="15" alt="*" />{/section}
{if $stars.part}<img src="{$images_dir}/icons/star_{$stars.part}.gif" width="16" height="15" alt="X" />{/if}{section name="full_star" loop=$stars.empty}<img src="{$images_dir}/icons/star_empty.gif" width="16" height="15" alt="o" />{/section}
