{* $Id: product_options_description.tpl 7806 2009-08-12 10:22:35Z alexions $ *}

{script src="js/picker.js"}
{script src="js/jquery.easydrag.js"}

{assign var="picker_name" value="product_option_picker_`$id`"}
{capture name="$picker_name"}
<div id="product_option_{$id}_content">
	<div class="object-container">	
		{$description|unescape}
	</div>
</div>
{/capture}
{include file="common_templates/popupbox.tpl" id="product_option_`$id`" text=$lang.description content=$smarty.capture.$picker_name link_text="$text" act="general" capture_link=$capture_link}