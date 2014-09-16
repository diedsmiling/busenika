{* $Id: fileuploader.tpl 10470 2010-08-20 11:03:37Z alexions $ *}

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
	custom_labels[id_var_name]['upload_another_file'] = {if $multiupload == "Y"}"{$upload_another_file_text|default:$lang.upload_another_file}"{else}"{$lang.local}"{/if};
	custom_labels[id_var_name]['upload_file'] = "{$upload_file_text|default:$lang.local}";
</script>

<div class="fileuploader">
<input type="hidden" id="{$label_id}" value="{if $images}{$id_var_name}{/if}" />

{foreach from=$images key="image_id" item="image"}
	<div class="upload-file-section cm-uploaded-image" id="message_{$id_var_name}_{$image.file}" title="">
		<p class="cm-fu-file">
			{hook name="fileuploader:links"}
				{if $image.location == "cart"}
					{assign var="delete_link" value="`$controller`.delete_file?cart_id=`$id`&amp;option_id=`$po.option_id`&amp;file=`$image_id`&amp;redirect_mode=cart"}
					{assign var="download_link" value="`$controller`.get_custom_file?cart_id=`$id`&amp;option_id=`$po.option_id`&amp;file=`$image_id`"}
				{/if}
			{/hook}
			{if $image.is_image}
				<a href="{$image.detailed|fn_url}" class="cm-thumbnails-opener"><img src="{$image.thumbnail|fn_url}" border="0" /></a><br />
			{/if}
			
			{hook name="fileuploader:uploaded_files"}
				{if $delete_link}<a class="cm-ajax" href="{$delete_link|fn_url}">{/if}{if !($po.required == "Y" && $images|count == 1)}<img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" hspace="3" id="clean_selection_{$id_var_name}_{$image.file}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" onclick="fileuploader.clean_selection(this.id); {if $multiupload != "Y"}fileuploader.toggle_links('{$id_var_name}', 'show');{/if} fileuploader.check_required_field('{$id_var_name}', '{$label_id}');" class="hand valign" />{/if}{if $delete_link}</a>{/if}<span>{if $download_link}<a href="{$download_link|fn_url}">{/if}{$image.name}{if $download_link}</a>{/if}</span>
			{/hook}
		</p>
	</div>
{/foreach}

<div class="nowrap" id="file_uploader_{$id_var_name}">
	<div class="upload-file-section" id="message_{$id_var_name}" title="">
		<p class="cm-fu-file hidden"><img src="{$images_dir}/icons/icon_delete.gif" width="12" height="18" border="0" hspace="3" id="clean_selection_{$id_var_name}" alt="{$lang.remove_this_item}" title="{$lang.remove_this_item}" onclick="fileuploader.clean_selection(this.id); {if $multiupload != "Y"}fileuploader.toggle_links(this.id, 'show');{/if} fileuploader.check_required_field('{$id_var_name}', '{$label_id}');" class="hand valign" /><span></span></p>
		{if $multiupload != "Y"}<p class="cm-fu-no-file {if $images}hidden{/if}">{$lang.text_select_file}</p>{/if}
	</div>
	
	{strip}
	<div class="select-field upload-file-links {if $multiupload != "Y" && $images}hidden{/if}" id="link_container_{$id_var_name}">
		<input type="hidden" name="file_{$var_name}" value="{if $image_name}{$image_name}{/if}" id="file_{$id_var_name}" {if $image}class="cm-image-field"{/if} />
		<input type="hidden" name="type_{$var_name}" value="{if $image_name}local{/if}" id="type_{$id_var_name}" {if $image}class="cm-image-field"{/if} />
		<div class="upload-file-local">
			<input type="file" name="file_{$var_name}" id="_local_{$id_var_name}" onchange="fileuploader.show_loader(this.id); {if $multiupload == "Y"}fileuploader.check_image(this.id);{/if} fileuploader.check_required_field('{$id_var_name}', '{$label_id}');" onclick="$(this).removeAttr('value');" value="" {if $image}class="cm-image-field"{/if} />
			<a id="local_{$id_var_name}">{if $images}{$upload_another_file_text|default:$lang.upload_another_file}{else}{$upload_file_text|default:$lang.local}{/if}</a>
		</div>&nbsp;&nbsp;|&nbsp;&nbsp;
		{if !$hide_server}
			<a onclick="fileuploader.show_loader(this.id);" id="server_{$id_var_name}">{$lang.server}</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		{/if}
		<a onclick="fileuploader.show_loader(this.id);" id="url_{$id_var_name}">{$lang.url}</a>
		
		{if $hidden_name}
			<input type="hidden" name="{$hidden_name}" value="{$hidden_value}">
		{/if}
	</div>
	{/strip}
</div>

</div><!--fileuploader-->