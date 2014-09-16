{* $Id: product_files.tpl 10123 2010-07-20 06:22:21Z andyye $ *}

{if $files}
<table cellspacing="1" cellpadding="5" class="table" width="30%">
<tr>
	<th>{$lang.filename}</th>
	<th>{$lang.filesize}</th>
</tr>
{foreach from=$files item="file"}
<tr>
	<td width="80">
		<a href="{"orders.get_file?file_id=`$file.file_id`&preview=Y"|fn_url}"><strong>{$file.file_name}</strong></a>
		{if $file.readme || $file.license}
		<ul class="bullets-list">
		{if $file.license}
			<li><a onclick="$('#license_{$file.file_id}').toggle(); return false;">{$lang.licence_agreement}</a></li>
			<div class="hidden" id="license_{$file.file_id}">{$file.license|unescape}</div>
		{/if}
		{if $file.readme}
			<li><a onclick="$('#readme_{$file.file_id}').toggle(); return false;">{$lang.readme}</a></li>
			<div class="hidden" id="readme_{$file.file_id}">{$file.readme|unescape}</div>
		{/if}
		</ul>
		{/if}
	</td>
	<td width="20%" valign="top">
		 <strong>{$file.file_size|formatfilesize}</strong>
	</td>
{/foreach}
</table>
{/if}