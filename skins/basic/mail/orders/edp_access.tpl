{* $Id: edp_access.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{include file="letter_header.tpl"}

{$lang.dear} {$order_info.firstname},<br /><br />

{$lang.edp_access_granted}<br /><br />

{foreach from=$order_info.items item="oi"}
{if $oi.extra.is_edp == 'Y' && $edp_data[$oi.product_id].files}
{assign var="first_file" value=$edp_data[$oi.product_id].files|reset}
<a href="{"orders.downloads?product_id=`$oi.product_id`&amp;ekey=`$first_file.ekey`"|fn_url:'C':'http':'&'}"><b>{$oi.product}</b></a><br />
<p></p>
{foreach from=$edp_data[$oi.product_id].files item="file" key="file_id"}
<a href="{"orders.get_file?file_id=`$file.file_id`&amp;product_id=`$oi.product_id`&amp;ekey=`$file.ekey`"|fn_url:'C':'http':'&'}">{$file.file_name} ({$file.file_size|number_format:0:'':' '}&nbsp;{$lang.bytes})</a><br /><br />
{/foreach}
{/if}
{/foreach}

{include file="letter_footer.tpl"}