{* $Id: breadcrumbs.tpl 9353 2010-05-04 06:10:09Z klerik $ *}
{if $breadcrumbs|@sizeof > 1}
<div class="breadcrumbs">{strip}
{* <a href="{$index_script}"><img src="{$images_dir}/icons/top_icon_home.gif" width="13" height="9" border="0" alt="" /></a> *}
	{foreach from=$breadcrumbs item="bc" name="bcn" key="key"}
	{if $key != "0"}<img src="{$images_dir}/breadcrumbs_arrow.gif" width="3" height="5" border="0" alt="" />{/if}
	{if $bc.link}
	<a href="{$bc.link|fn_url}">{$bc.title}</a>
	{else}
		<strong>{$bc.title}</strong>
	{/if}
	{/foreach}{/strip}
</div>
{/if}