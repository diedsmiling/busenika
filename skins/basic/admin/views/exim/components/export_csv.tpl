{* $Id: export_csv.tpl 6020 2008-09-29 08:24:03Z zeke $ *}
{if $fields}{$delimiter|implode:$fields|unescape}{$eol}{/if}{foreach from=$export_data item=data}{$delimiter|implode:$data|unescape}{$eol}{/foreach}
