{* $Id: tabs_block.post.tpl 6483 2008-12-03 14:57:53Z zeke $ *}
{** block-description:attachments **}

{if $attachments_data}
<div id="content_attachments">
{foreach from=$attachments_data item="file"}
<p>
{$file.description} ({$file.filename}, {$file.filesize|formatfilesize}) [<a href="{"attachments.getfile?attachment_id=`$file.attachment_id`"|fn_url}">{$lang.download}</a>]
</p>
{/foreach}
</div>
{/if}