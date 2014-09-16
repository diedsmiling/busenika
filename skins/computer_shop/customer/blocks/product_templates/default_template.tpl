
{script src="js/exceptions.js"}

<div class="product-main-info">
{hook name="products:view_main_info"}

	{if $product}
	{assign var="obj_id" value=$product.product_id}
	{include file="common_templates/product_data.tpl" product=$product}
	{assign var="form_open" value="form_open_`$obj_id`"}
	{$smarty.capture.$form_open}
	<div class="clear">
		{if !$no_images}
			<div class="image-border float-left center cm-reload-{$product.product_id}" id="product_images_{$product.product_id}_update">
				{include file="views/products/components/product_images.tpl" product=$product show_detailed_link="Y"}
			<!--product_images_{$product.product_id}_update--></div>
		{/if}
		
		<div class="product-info">
			<h1 class="mainbox-title">{$product.product|unescape}</h1>
			{assign var="rating" value="rating_`$obj_id`"}{$smarty.capture.$rating}
			{assign var="sku" value="sku_$obj_id"}{$smarty.capture.$sku}
			<hr class="dashed clear-both" />
		
			{assign var="old_price" value="old_price_`$obj_id`"}
			{assign var="price" value="price_`$obj_id`"}
			{assign var="clean_price" value="clean_price_`$obj_id`"}
			{assign var="list_discount" value="list_discount_`$obj_id`"}
			{assign var="discount_label" value="discount_label_`$obj_id`"}
			<div class="{if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}prices-container {/if}clear">
			{if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}
				<div class="float-left product-prices">
					{if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price}&nbsp;{/if}
			{/if}
			
			{if !$smarty.capture.$old_price|trim || $details_page}<p>{/if}
					{$smarty.capture.$price}
			{if !$smarty.capture.$old_price|trim || $details_page}</p>{/if}
		
			{if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}
					{$smarty.capture.$clean_price}
					{$smarty.capture.$list_discount}
				</div>
			{/if}
			{if $show_discount_label && $smarty.capture.$discount_label|trim}
				<div class="float-left">
					{$smarty.capture.$discount_label}
				</div>
			{/if}
			</div>
		
			{if $capture_options_vs_qty}{capture name="product_options"}{/if}
			
			{assign var="product_amount" value="product_amount_`$obj_id`"}
			{$smarty.capture.$product_amount}
			
			{assign var="product_options" value="product_options_`$obj_id`"}
			{$smarty.capture.$product_options}
			
			{assign var="qty" value="qty_`$obj_id`"}
			{$smarty.capture.$qty}
			
			{assign var="advanced_options" value="advanced_options_`$obj_id`"}
			{$smarty.capture.$advanced_options}
			{if $capture_options_vs_qty}{/capture}{/if}
		
			{assign var="min_qty" value="min_qty_`$obj_id`"}
			{$smarty.capture.$min_qty}
			
			{assign var="product_edp" value="product_edp_`$obj_id`"}
			{$smarty.capture.$product_edp}

			{if $capture_buttons}{capture name="buttons"}{/if}
				<div class="buttons-container nowrap">
					{assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
					{$smarty.capture.$add_to_cart}
					
					{assign var="list_buttons" value="list_buttons_`$obj_id`"}
					{$smarty.capture.$list_buttons}
				</div>
			{if $capture_buttons}{/capture}{/if}
		</div>
	</div>
	{assign var="form_close" value="form_close_`$obj_id`"}
	{$smarty.capture.$form_close}
	{/if}
				<div class="social-widgets">			
						<div class="facebook-div">
						<div id="fb-root"></div>			
						<div class="fb-like" data-send="false" data-layout="button_count" data-width="100" data-show-faces="true"></div>
					</div>
					<div class="vk-div">
						<div id="vk_like1"></div>
						{literal}
						<script type="text/javascript">
							VK.Widgets.Like("vk_like1", {type: "mini", pageUrl: "http://www.100servizov.ru/{/literal}{php}echo $_SERVER['REQUEST_URI'];{/php}{literal}"});
							
						</script>
						{/literal}
					</div>
				
					<div class="twitter-div">
						{literal}<a href="https://twitter.com/share" class="twitter-share-button" data-via="diedsmilling" data-lang="ru">Твитнуть</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						{/literal}
					</div>
					<div class="ok-div">
						{literal}
						<a target="_blank" class="mrc__plugin_uber_like_button" href="http://connect.mail.ru/share" data-mrc-config="{'cm' : '1', 'ck' : '3', 'sz' : '20', 'st' : '3', 'tp' : 'ok'}">Нравится</a>
						<script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script>
						{/literal}
					</div>
				
			</div>
{/hook}

{capture name="tabsbox"}
	{assign var="but_role" value=""}
	{assign var="tabs_block_orientation" value=$blocks.$tabs_block_id.properties.block_order}
	{foreach from=$blocks item="block" key="block_id"}
		{if $block.group_id == $tabs_block_id}
			{assign var="tabs_capture_name" value="tab_`$block_id`"}
			{capture name=$tabs_capture_name}
				{block id=$block_id no_box=true}
			{/capture}
			{assign var="nav_block_id" value="block_`$block_id`"}
			{if $smarty.capture.$tabs_capture_name|trim}
				{if $tabs_block_orientation == "V"}
					<h1 class="tab-list-title">{$navigation.tabs.$nav_block_id.title}</h1>
				{/if}
			{/if}

			<div id="content_block_{$block_id}" class="wysiwyg-content{if $hide_tab && $tabs_block_orientation == "H"} hidden{/if}">
				{$smarty.capture.$tabs_capture_name}
			</div>
			{if $smarty.capture.$tabs_capture_name|trim}
				{assign var="hide_tab" value=true}
			{/if}
		{/if}
	{/foreach}
{/capture}

{capture name="tabsbox_content"}
{if $tabs_block_orientation == "V"}
	{$smarty.capture.tabsbox}
{else}
	{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab="block_`$smarty.request.selected_section`"}
{/if}
{/capture}

{if $blocks.$tabs_block_id.properties.wrapper}
	{include file=$blocks.$tabs_block_id.properties.wrapper content=$smarty.capture.tabsbox_content title=$blocks.$tabs_block_id.description}
{else}
	{$smarty.capture.tabsbox_content}
{/if}
</div>

<div class="product-details">
</div>

{capture name="mainbox_title"}{assign var="details_page" value=true}{/capture}
