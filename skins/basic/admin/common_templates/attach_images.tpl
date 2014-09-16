{* $Id: attach_images.tpl 10424 2010-08-16 08:47:46Z alexions $ *}

{*
image_key - required
image_name - required
image_object_type - required

image_object_id - optional
image_type - optional
*}

{if !"SMARTY_ATTACH_IMAGES_LOADED"|defined}
{assign var="tmp" value="SMARTY_ATTACH_IMAGES_LOADED"|define:true}
<script type="text/javascript">
	//<![CDATA[
	{literal}
	function fn_delete_image(r, p)
	{
		if (r.deleted == true) {
			$('#' + p.result_ids).replaceWith('<img border="0" src="' + images_dir + '/no_image.gif" />');
			$('a[rev=' + p.result_ids + ']').hide();
		}
	}
	
	function fn_delete_image_pair(r, p)
	{
		if (r.deleted == true) {
			$('#' + p.result_ids).remove();
		}
	}
	{/literal}
	//]]>
</script>
{/if}

{assign var="_plug" value="."|explode:""}
{assign var="key" value=$image_key|default:"0"}
{assign var="object_id" value=$image_object_id|default:"0"}
{assign var="name" value=$image_name|default:""}
{assign var="object_type" value=$image_object_type|default:""}
{assign var="type" value=$image_type|default:"M"}
{assign var="pair" value=$image_pair|default:$_plug}
{assign var="suffix" value=$image_suffix|default:""}

<input type="hidden" name="{$name}_image_data{$suffix}[{$key}][pair_id]" value="{$pair.pair_id}" class="cm-image-field" />
<input type="hidden" name="{$name}_image_data{$suffix}[{$key}][type]" value="{$type|default:"M"}" class="cm-image-field" />
<input type="hidden" name="{$name}_image_data{$suffix}[{$key}][object_id]" value="{$object_id}" class="cm-image-field" />

<div id="box_attach_images_{$name}_{$key}">
	{if $no_thumbnail && !$pair.icon}
		{$lang.text_thumbnail_manual_loading}&nbsp;<a id="sw_load_thumbnail_{$name}{$suffix}{$key}" class="cm-combination dashed">{$lang.thumbnail_manual_loading_link}</a>
	{/if}
	<div class="clear {if $no_thumbnail && !$pair.icon}hidden{/if}" id="load_thumbnail_{$name}{$suffix}{$key}">
	{if $delete_pair && $pair.pair_id}
		<div class="float-right">
			<a rev="box_attach_images_{$name}_{$key}" href="{"image.delete_image_pair?pair_id=`$pair.pair_id`&amp;object_type=`$object_type`"|fn_url}" class="cm-confirm cm-ajax delete" name="delete_image_pair">{$lang.delete_image_pair}</a>
		</div>
	{/if}
		{if !$hide_titles}
			<p>
				<span class="field-name">{$icon_title|default:$lang.thumbnail}</span>
				{if $icon_text}<span class="small-note">{$icon_text}</span>{/if}
				<span class="field-name">:</span>
			</p>
		{/if}
		
		{if !$hide_images}
			<div class="float-left image">
				{include file="common_templates/image.tpl" image=$pair.icon image_id=$pair.image_id image_width=85 object_type=$object_type}
				{if $pair.image_id}
				<p>
					<a rev="image_{$object_type}_{$pair.image_id}" href="{"image.delete_image?pair_id=`$pair.pair_id`&amp;image_id=`$pair.image_id`&amp;object_type=`$object_type`"|fn_url}" class="cm-confirm cm-ajax delete" name="delete_image">{$lang.delete_image}</a>
				</p>
				{/if}
			</div>
		{/if}
		
		<div class="float-left attach-images-alt">
			{include file="common_templates/fileuploader.tpl" var_name="`$name`_image_icon`$suffix`[`$key`]" image=true}
			{if !$hide_alt}
			<label for="alt_icon_{$name}_{$key}">{$lang.alt_text}:</label>
			<input type="text" class="input-text cm-image-field" id="alt_icon_{$name}_{$key}" name="{$name}_image_data{$suffix}[{$key}][image_alt]" value="{$pair.icon.alt}" />
			{/if}
		</div>
	</div>
	
	{if !$no_detailed}
	<div class="clear margin-top">
		{if !$hide_titles}
			<p>
				<span class="field-name">{$detailed_title|default:$lang.popup_larger_image}</span>
				{if $detailed_text}
					<span class="small-note">{$detailed_text}</span>
				{/if}
				<span class="field-name">:</span>
			</p>
		{/if}
		
		{if !$hide_images}
			<div class="float-left image">
				{include file="common_templates/image.tpl" image=$pair.detailed image_id=$pair.detailed_id image_width=85 object_type="detailed"}
				{if $pair.detailed_id}
				<p>
					<a rev="image_detailed_{$pair.detailed_id}" href="{"image.delete_image?pair_id=`$pair.pair_id`&amp;image_id=`$pair.detailed_id`&amp;object_type=detailed"|fn_url}" class="cm-confirm cm-ajax delete" name="delete_image">{$lang.delete_image}</a>
				</p>
				{/if}
			</div>
		{/if}
		
		<div class="float-left attach-images-alt">
			{include file="common_templates/fileuploader.tpl" var_name="`$name`_image_detailed`$suffix`[`$key`]"}
			{if !$hide_alt}
			<label for="alt_det_{$name}_{$key}">{$lang.alt_text}:</label>
			<input type="text" class="input-text cm-image-field" id="alt_det_{$name}_{$key}" name="{$name}_image_data{$suffix}[{$key}][detailed_alt]" value="{$pair.detailed.alt}" />
			{/if}
		</div>
	</div>
	{/if}
</div>