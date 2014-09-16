{* $Id: file_browser_thumbnails.tpl 9957 2010-07-02 13:51:18Z lexa $ *}

<div class="cm-filetree">
{foreach from=$file_list item="file"}
	{if $file.ext}
		<div class="file ext_{$file.ext} cm-selectable"><a rel="{$current_dir}{$file.file}" title="{$file.file}">
			<div class="img-border">
			{assign var="image_path" value="`$current_dir``$file.file`"}
			{assign var="icon_image_path" value=$image_path|fn_generate_thumbnail:90:90:true}
			{if $icon_image_path}
				<img src="{$icon_image_path}" width="90" height="90" alt="" border="0" />
			{else}
				<img src="{$images_dir}/ext/{$file.ext}.png" width="90" height="90" alt="" border="0" onerror="this.src = '{$images_dir}/ext/file.png';" />
			{/if}
			</div>
			<p>{$file.file}</p>
		</a></div>
	{/if}
{/foreach}
</div>