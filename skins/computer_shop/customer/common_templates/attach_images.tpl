{* $Id: attach_images.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

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
	lang['delete_image_warning'] = '{$lang.delete_image_warning|escape:"javascript"}';
	lang['delete_image_pair_warning'] = '{$lang.delete_image_pair_warning|escape:"javascript"}';
	
	{literal}
	function fn_delete_image(pair_id, image_id, object_type)
	{
		var elm_id = 'image_' + object_type + '_' + image_id;
		if (document.getElementById(elm_id)) {
			if (confirm(lang['delete_image_warning'])) {
				{/literal}
				document.getElementById(elm_id).src = '{"image.delete_image?pair_id="|fn_url:'C':'rel':'&'}' + pair_id + '&image_id=' + image_id + '&object_type=' + object_type;
				{literal}
				document.getElementById('delete_' + elm_id).style.display = 'none';
			}
		}
	}
	
	function fn_delete_image_pair(elm_id, pair_id, object_type)
	{
		if (document.getElementById('box_' + elm_id)) {
			if (confirm(lang['delete_image_pair_warning'])) {
				var image = new Image();
				{/literal}
				image.src = '{"image.delete_image_pair?pair_id="|fn_url:'C':'rel':'&'}' + pair_id + '&object_type=' + object_type;
				{literal}
				document.getElementById('box_' + elm_id).style.display = 'none';
			}
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
	<div class="clear">
		{if !$hide_titles}
			<p>
				<span class="field-name">{$icon_title|default:$lang.thumbnail}</span>
				{if $icon_text}<span class="small-note">{$icon_text}</span>{/if}
				<span class="field-name">:</span>
			</p>
		{/if}
		
		{if !$hide_images}
			<div class="float-left image">
				{include file="common_templates/image.tpl" image=$pair.icon image_id=$pair.image_id image_width="85" object_type=$object_type}
				{if $pair.image_id}
				<p>{include file="buttons/button.tpl" but_id="delete_image_`$object_type`_`$pair.image_id`" but_text=$lang.delete_image but_href="javascript: fn_delete_image('`$pair.pair_id`', '`$pair.image_id`', '`$object_type`');" but_role="delete"}</p>
				{/if}
			</div>
		{/if}
		
		<div class="float-left">
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
				{include file="common_templates/image.tpl" image=$pair.detailed image_id=$pair.detailed_id image_width="85" object_type="detailed"}
				{if $pair.detailed_id}
				<p>{include file="buttons/button.tpl" but_id="delete_image_detailed_`$pair.detailed_id`" but_text=$lang.delete_image but_href="javascript: fn_delete_image('`$pair.pair_id`', '`$pair.detailed_id`', 'detailed');" but_role="delete"}</p>
				{/if}
			</div>
		{/if}
		
		<div class="float-left">
			{include file="common_templates/fileuploader.tpl" var_name="`$name`_image_detailed`$suffix`[`$key`]"}
			{if !$hide_alt}
			<label for="alt_det_{$name}_{$key}">{$lang.alt_text}:</label>
			<input type="text" class="input-text cm-image-field" id="alt_det_{$name}_{$key}" name="{$name}_image_data{$suffix}[{$key}][detailed_alt]" value="{$pair.detailed.alt}" />
			{/if}
		</div>
	</div>
	{/if}
	{if $delete_pair && $pair.pair_id}
		<p>
			{include file="buttons/button.tpl" but_id="attach_images_`$name`_`$key`" but_text=$lang.delete_image_pair but_href="javascript: void(0);" but_onclick="fn_delete_image_pair(this.id, '`$pair.pair_id`', '`$object_type`');" but_role="text"}
		</p>
	{/if}
</div>
