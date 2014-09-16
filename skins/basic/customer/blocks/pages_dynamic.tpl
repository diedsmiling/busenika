{* $Id: pages_dynamic.tpl 8351 2009-12-03 13:38:37Z angel $ *}
{** block-description:dynamic **}

{if $items}
<ul>
{include file="views/pages/components/pages_tree.tpl" tree=$items root=true}
</ul>
{/if}
