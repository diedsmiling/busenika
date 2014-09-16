{* $Id: categories_emenu.tpl 9783 2010-06-10 10:24:09Z lexa $ *}
{** block-description:emenu **}

<div class="clear">
	<ul id="vmenu_{$block.block_id}" class="dropdown dropdown-vertical">
		{include file="views/categories/components/menu_items.tpl" items=$items separated=true submenu=false}
	</ul>
</div>
