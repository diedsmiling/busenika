{* $Id: update.tpl 6850 2009-01-30 13:32:06Z lexa $ *}

{capture name="mainbox"}
{if $banner.code && $banner_type != "P"}
	<div class="float-right">{$banner.code|unescape}</div>
{/if}

{if $banner_type == "T"}
	{include file="addons/affiliate/views/banners_manager/components/text_banner_update.tpl"}
{elseif $banner_type == "G"}
	{include file="addons/affiliate/views/banners_manager/components/graphic_banner_update.tpl"}
{elseif $banner_type == "P"}
	{include file="addons/affiliate/views/banners_manager/components/product_banner_update.tpl"}
{/if}

{/capture}

{if $mode == "add"}
	{include file="common_templates/mainbox.tpl" title=$lang.new_banner content=$smarty.capture.mainbox tools=$smarty.capture.tools select_languages=true}
{else}
	{include file="common_templates/mainbox.tpl" title="`$lang.editing_banner`: `$banner.title`" content=$smarty.capture.mainbox select_languages=true}
{/if}
