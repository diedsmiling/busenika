{* $Id: original.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{** block-description:original **}
{foreach from=$items item="banner" key="key"}
	{if $banner.type == "G" && $banner.main_pair.image_id}
	<div class="ad-container center">
		{if $banner.url != ""}<a href="{$banner.url|fn_url}" {if $banner.target == "B"}target="_blank"{/if}>{/if}
		{include file="common_templates/image.tpl" images=$banner.main_pair object_type="common"}
		{if $banner.url != ""}</a>{/if}
	</div>
	{else}
		{$banner.description|unescape}
	{/if}
{/foreach}