{* $Id: file_browser_dirs.tpl 9520 2010-05-19 14:50:42Z zeke $ *}

<ul class="cm-filetree">
{foreach from=$file_list item="file"}
	{if $file.ext}
		<li class="file ext_{$file.ext} cm-selectable" ondblclick="fileuploader.set_file('{$current_dir|escape:"javascript"}{$file.file|escape:"javascript"}', false);"><a rel="{$current_dir}{$file.file}">{$file.file}</a></li>
	{else}
		{if $file.next}
			<li class="directory cm-expanded"><a rel="{$current_dir}{$file.file}/">{$file.file}</a>
			{include file="common_templates/file_browser_dirs.tpl" file_list=$file.next current_dir="`$current_dir``$file.file`/"}</li>
		{else}
			<li class="directory cm-collapsed"><a rel="{$current_dir}{$file.file}/">{$file.file}</a></li>
		{/if}
	{/if}
{/foreach}
</ul>