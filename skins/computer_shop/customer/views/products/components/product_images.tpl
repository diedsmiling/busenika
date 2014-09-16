{* $Id: product_images.tpl 10538 2010-08-30 08:53:19Z alexions $ *}

{assign var="th_size" value="30"}
{include file="common_templates/previewer.tpl"}
{if $product.main_pair.icon || $product.main_pair.detailed}
	{assign var="image_pair_var" value=$product.main_pair}
{elseif $product.option_image_pairs}
	{assign var="image_pair_var" value=$product.option_image_pairs|reset}
{/if}
{include file="common_templates/image.tpl" obj_id=$product.product_id images=$image_pair_var object_type="detailed_product" class="cm-thumbnails" show_thumbnail="Y" image_width=$settings.Thumbnails.product_details_thumbnail_width image_height=$settings.Thumbnails.product_details_thumbnail_height}

{foreach from=$product.image_pairs item="image_pair"}
	{if $image_pair}
		{if $image_pair.image_id == 0}
			{assign var="image_id" value=$image_pair.detailed_id}
		{else}
			{assign var="image_id" value=$image_pair.image_id}
		{/if}
		{include file="common_templates/image.tpl" images=$image_pair object_type="detailed_product" class="cm-thumbnails hidden" show_thumbnail="Y" detailed_link_class="hidden" obj_id="`$product.product_id`_`$image_id`" image_width=$settings.Thumbnails.product_details_thumbnail_width image_height=$settings.Thumbnails.product_details_thumbnail_height}
	{/if}
{/foreach}

{if $image_pair_var && $product.image_pairs}
	{if $settings.Appearance.thumbnails_gallery == "Y"}
	{strip}
		<ul id="product_thumbnails" class="center jcarousel-skin hidden">
			<li>
				<a class="cm-thumbnails-mini">{include file="common_templates/image.tpl" images=$image_pair_var object_type="detailed_product" link_class="cm-thumbnails-mini cm-cur-item" image_width=$th_size image_height=$th_size show_thumbnail="Y" show_detailed_link=false obj_id="`$product.product_id`_mini" make_box=true}</a>
			</li>
			{foreach from=$product.image_pairs item="image_pair"}
				{if $image_pair}
					<li>
						{if $image_pair.image_id == 0}
							{assign var="image_id" value=$image_pair.detailed_id}
						{else}
							{assign var="image_id" value=$image_pair.image_id}
						{/if}
						<a class="cm-thumbnails-mini">{include file="common_templates/image.tpl" images=$image_pair object_type="detailed_product" link_class="cm-thumbnails-mini" image_width=$th_size image_height=$th_size show_thumbnail="Y" show_detailed_link=false obj_id="`$product.product_id`_`$image_id`_mini" make_box=true}</a>
					</li>
				{/if}
			{/foreach}
		</ul>
		{/strip}
		
		{script src="js/jquery.jcarousel.js"}
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function() {$ldelim}
			$('#product_thumbnails').show();
			{if $product.image_pairs|count > 2}
				$('#product_thumbnails').removeClass('hidden');
				var i_width = $('.cm-thumbnails-mini').outerWidth(true);
				var c_width = i_width * 3;
				var i_height = $('.cm-thumbnails-mini').outerHeight(true);
				$('#product_thumbnails').jcarousel({$ldelim}
					scroll: 1,
					wrap: 'circular',
					animation: 'fast',
					initCallback: fn_scroller_init_callback,
					itemVisibleOutCallback: {$ldelim}onAfterAnimation: fn_scroller_next_callback, onBeforeAnimation: fn_scroller_prev_callback{$rdelim},
					item_width: i_width,
					item_height: i_height,
					clip_width: c_width,
					clip_height: i_height,
					buttonNextHTML: '<div></div>',
					buttonPrevHTML: '<div></div>',
					buttonNextEvent: 'click',
					buttonPrevEvent: 'click',
					item_count: {$product.image_pairs|count} + 1
				{$rdelim});
				$('.jcarousel-skin').css({$ldelim}'width': c_width + $('.jcarousel-prev-horizontal').outerWidth(true) * 2 + 'px'{$rdelim});
			{/if}
		{$rdelim});
		//]]>
		</script>
	{else}
		<div class="center" style="width: {$settings.Thumbnails.product_details_thumbnail_width}px;">
		{strip}
			<a class="cm-thumbnails-mini">{include file="common_templates/image.tpl" images=$image_pair_var object_type="detailed_product" link_class="cm-thumbnails-mini cm-cur-item" image_width=$th_size image_height=$th_size show_thumbnail="Y" show_detailed_link=false obj_id="`$product.product_id`_mini" make_box=true}</a>
			{foreach from=$product.image_pairs item="image_pair"}
				{if $image_pair}
						{if $image_pair.image_id == 0}
							{assign var="image_id" value=$image_pair.detailed_id}
						{else}
							{assign var="image_id" value=$image_pair.image_id}
						{/if}
						<a class="cm-thumbnails-mini">{include file="common_templates/image.tpl" images=$image_pair object_type="detailed_product" link_class="cm-thumbnails-mini" image_width=$th_size show_thumbnail="Y" show_detailed_link=false obj_id="`$product.product_id`_`$image_id`_mini" make_box=true}</a>
				{/if}
			{/foreach}
		{/strip}
	    </div>
	{/if}
{/if}
