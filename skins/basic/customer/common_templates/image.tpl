{* $Id: image.tpl 10506 2010-08-25 08:50:54Z alexions $ *}
{strip}

{if $capture_image}
	{capture name="image"}
{/if}

{if $show_thumbnail != "Y"}
	{if !$image_width}
		{if $images.icon.image_x}
			{assign var="image_width" value=$images.icon.image_x}
		{/if}
		{if $images.icon.image_y}
			{assign var="image_height" value=$images.icon.image_y}
		{/if}
		{if !$image_width || !$image_height}
			{if $images.detailed.image_x}
				{assign var="image_width" value=$images.detailed.image_x}
			{/if}
			{if $images.detailed.image_y}
				{assign var="image_height" value=$images.detailed.image_y}
			{/if}
		{/if}
	{else}
		{if $images.icon.image_x && $images.icon.image_y}
			{math equation="new_x * y / x" new_x=$image_width x=$images.icon.image_x y=$images.icon.image_y format="%d" assign="image_height"}
		{/if}
		{if !$image_height && $images.detailed.image_x && $images.detailed.image_y}
			{math equation="new_x * y / x" new_x=$image_width x=$images.detailed.image_x y=$images.detailed.image_y format="%d" assign="image_height"}
		{/if}
	{/if}
{/if}

{if $images.icon}
	{assign var="image_id" value=$images.image_id}
{elseif $images.detailed}
	{assign var="image_id" value=$images.detailed_id}
{/if}

{if $show_thumbnail == "Y" && ($image_width || $image_height) && $image_id}
	{if $image_width && $image_height}
		{assign var="make_box" value=true}
		{assign var="proportional" value=true}
	{/if}
	{assign var="object_type" value=$object_type|default:"product"}
	{if $images.icon.image_path}
		{assign var="image_path" value=$images.icon.image_path}
	{else}
		{assign var="image_path" value=$images.detailed.image_path}
	{/if}

	{assign var="icon_image_path" value=$image_path|fn_generate_thumbnail:$image_width:$image_height:$make_box}
	{if $make_box && !$proportional}
		{assign var="image_height" value=$image_width}
	{/if}
{else}
	{assign var="icon_image_path" value=$images.icon.image_path}
	{if !$icon_image_path}
		{if $object_type == "detailed_product" && $images.detailed.image_x}
			{if $settings.Thumbnails.product_details_thumbnail_width}
				{assign var="image_width" value=$settings.Thumbnails.product_details_thumbnail_width}
				{if $make_box && !$proportional}
					{assign var="image_height" value=$image_width}
				{else}
					{math equation="new_x * y / x" new_x=$image_width x=$images.detailed.image_x y=$images.detailed.image_y format="%d" assign="image_height"}
				{/if}
			{/if}
		{/if}
		{assign var="icon_image_path" value=$images.detailed.image_path|fn_generate_thumbnail:$image_width:$image_height:$make_box}
	{/if}
{/if}

{if !$images.icon.is_flash}
	{if $show_detailed_link && $images.detailed_id}
		{if $object_type == "detailed_product" && ($settings.Thumbnails.product_detailed_image_width || $settings.Thumbnails.product_detailed_image_height)}
			{assign var="detailed_image_path" value=$images.detailed.image_path|fn_generate_thumbnail:$settings.Thumbnails.product_detailed_image_width:$settings.Thumbnails.product_detailed_image_height:$make_box}
		{elseif $object_type == "detailed_category" && ($settings.Thumbnails.category_detailed_image_width || $settings.Thumbnails.category_detailed_image_height)}
			{assign var="detailed_image_path" value=$images.detailed.image_path|fn_generate_thumbnail:$settings.Thumbnails.category_detailed_image_width:$settings.Thumbnails.category_detailed_image_height:$make_box}
		{else}
			{assign var="detailed_image_path" value=$images.detailed.image_path}
		{/if}
		<a{if $obj_id && !$no_ids} id="detailed_href1_{$obj_id}"{/if}{if $rel} rel="{$rel}"{/if}{if $link_class} class="{$link_class}"{/if} href="{$detailed_image_path}" rev="{$images.detailed.alt}">
	{/if}

	{if !(($object_type == "category" || $object_type == "detailed_category") && !$icon_image_path)}
	<img class="{$valign} {$class}" {if $obj_id && !$no_ids}id="det_img_{$obj_id}"{/if} src="{$icon_image_path|default:$config.no_image_path}" {if $image_width}width="{$image_width}"{/if} {if $image_height}height="{$image_height}"{/if} alt="{$images.icon.alt}" {if $image_onclick}onclick="{$image_onclick}"{/if} border="0" />
	{/if}
	
	{if $show_detailed_link && $images.detailed_id}
		</a>
	{/if}
{else}
<object {if $valign}class="valign"{/if} classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" {if $image_width}width="{$image_width}"{/if} {if $image_height}height="{$image_height}"{/if}>
<param name="movie" value="{$images.icon.image_path|default:$config.no_image_path}" />
<param name="quality" value="high" />
<param name="wmode" value="transparent" />
<param name="allowScriptAccess" value="sameDomain" />
{if $flash_vars}
<param name="FlashVars" value="{$flash_vars}">
{/if}
<embed src="{$images.icon.image_path|default:$config.no_image_path}" quality="high" wmode="transparent" {if $image_width}width="{$image_width}"{/if} {if $image_height}height="{$image_height}"{/if} allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" {if $flash_vars}FlashVars="{$flash_vars}"{/if} />
</object>
{/if}

{if $show_detailed_link}
<p class="{if !$images.detailed_id}hidden{/if} {$detailed_link_class} center" id="detailed_box_{$obj_id}">
<a {if $obj_id && !$no_ids}id="detailed_href2_{$obj_id}"{/if} href="{$images.detailed.image_path}" class="cm-thumbnails-opener view-larger-image">{$lang.view_larger_image}</a>
</p>
{/if}

{if $capture_image}
	{/capture}
	{capture name="icon_image_path"}
		{$icon_image_path}
	{/capture}
	{capture name="detailed_image_path"}
		{$detailed_image_path}
	{/capture}
{/if}

{/strip}