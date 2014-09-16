{* $Id: fileuploader.tpl 9721 2010-06-04 11:17:55Z lexa $ *}

{script src="js/fileuploader_scripts.js"}
{script src="js/node_cloning.js"}
{script src="js/previewer.js"}

{assign var="id_var_name" value=$prefix|cat:$var_name|md5}

<script type="text/javascript">
	var id_var_name = "{$id_var_name}";
	var label_id = "{$label_id}";
	
	if (typeof(custom_labels) == "undefined") {$ldelim}
		custom_labels = {$ldelim}{$rdelim};
	{$rdelim}
	
	custom_labels[id_var_name] = {$ldelim}{$rdelim};
	custom_labels[id_var_name]['upload_another_file'] = "{$upload_another_file_text|default:$lang.upload_another_file}";
	custom_labels[id_var_name]['upload_file'] = "{$upload_file_text|default:$lang.upload_file}";
</script>

<div class="fileuploader">
<input type="hidden" id="{$label_id}" value="{if $images}{$id_var_name}{/if}" />

{foreach from=$images key="image_id" item="image"}
	<div class="upload-file-section cm-uploaded-image" id="message_{$id_var_name}_{$image.file}" title="">
		<p class="cm-fu-file">
			{hook name="fileuploader:links"}
				{if $image.location == "cart"}
					{assign var="delete_link" value="checkout.delete_file?cart_id=`$id`&amp;option_id=`$po.option_id`&amp;file=`$image_id`&amp;redirect_mode=cart"}
					{assign var="download_link" value="checkout.get_custom_file?cart_id=`$id`&amp;option_id=`$po.option_id`&amp;file=`$image_id`"}
				{/if}
			{/hook}
			{if $image.is_image}
				<a href="{$image.detailed|fn_url}" class="cm-thumbnails-opener"><img class="cm-thumbnails cm-single" src="{$image.thumbnail|fn_url}" border="0" /></a><br />
			{/if}
			
			{hook name="fileuploader:uploaded_files"}
				{if $delete_link}<a class="cm-ajax" href="{$delete_link|fn_url}">{/if}{if !($po.required == "Y" && $images|count == 1)}<img src="{$images_dir}/icons/icon_delete.gif" width="12" height="12" border="0" hspace="3" id="clean_selection_{$id_var_name}_{$image.file}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" onclick="fileuploader.clean_selection(this.id); {if $multiupload != "Y"}fileuploader.toggle_links('{$id_var_name}', 'show');{/if} fileuploader.check_required_field('{$id_var_name}', '{$label_id}');" class="hand valign" />{/if}{if $delete_link}</a>{/if}<span>{if $download_link}<a href="{$download_link|fn_url}">{/if}{$image.name}{if $download_link}</a>{/if}</span>
			{/hook}
		</p>
	</div>
{/foreach}

<div class="nowrap" id="file_uploader_{$id_var_name}">
	<div class="upload-file-section" id="message_{$id_var_name}" title="">
		<p class="cm-fu-file hidden"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="12" border="0" hspace="3" id="clean_selection_{$id_var_name}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" onclick="fileuploader.clean_selection(this.id); {if $multiupload != "Y"}fileuploader.toggle_links(this.id, 'show');{/if} fileuploader.check_required_field('{$id_var_name}', '{$label_id}');" class="hand valign" /><span></span></p>
	</div>
	
	{strip}
	<div class="select-field upload-file-links {if $multiupload != "Y" && $images}hidden{/if}" id="link_container_{$id_var_name}">
		<input type="hidden" name="file_{$var_name}" value="{if $image_name}{$image_name}{/if}" id="file_{$id_var_name}" />
		<input type="hidden" name="type_{$var_name}" value="{if $image_name}local{/if}" id="type_{$id_var_name}" />
		<div class="upload-file-local">
			<input type="file" name="file_{$var_name}" id="_local_{$id_var_name}" onchange="fileuploader.show_loader(this.id); {if $multiupload == "Y"}fileuploader.check_image(this.id);{else}fileuploader.toggle_links(this.id, 'hide');{/if} fileuploader.check_required_field('{$id_var_name}', '{$label_id}');" onclick="$(this).removeAttr('value');" value="" />
			<a id="local_{$id_var_name}">{if $images}{$upload_another_file_text|default:$lang.upload_another_file}{else}{$upload_file_text|default:$lang.upload_file}{/if}</a>
		</div>
		{if $allow_url_uploading}
			&nbsp;{$lang.or}&nbsp;
			<a onclick="fileuploader.show_loader(this.id); {if $multiupload == "Y"}fileuploader.check_image(this.id);{else}fileuploader.toggle_links(this.id, 'hide');{/if} fileuploader.check_required_field('{$id_var_name}', '{$label_id}');" id="url_{$id_var_name}">{$lang.specify_url}</a>
		{/if}
		{if $hidden_name}
			<input type="hidden" name="{$hidden_name}" value="{$hidden_value}">
		{/if}
	</div>
	{/strip}
</div>

</div><!--fileuploader-->