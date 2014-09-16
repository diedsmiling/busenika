{* $Id: slider.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

{** block-description:slider **}
<script type="text/javascript" src="/js/slides.min.jquery.js"></script>
<div id="slides">
	<div class="slides_container">		
		{foreach from=$items item="banner" key="key"}

			{if $banner.type == "G" && $banner.main_pair.image_id}
			
				{if $banner.url != ""}<a href="{$banner.url|fn_url}" {if $banner.target == "B"}target="_blank"{/if}>{/if}
				{include file="common_templates/image.tpl" images=$banner.main_pair object_type="common"}
				<span>{$banner.banner}</span>
				{if $banner.url != ""}</a>{/if}
			
			{else}
				{$banner.description|unescape}
			{/if}
		{/foreach}
	</div>
	<a href="#" class="prev"></a>
	<a href="#" class="next"></a>
</div>			
