{* $Id: price.tpl 8344 2009-12-02 09:06:26Z alexions $ *}
{strip}
{if $settings.General.alternative_currency == "Y"}
	{$value|format_price:$currencies.$primary_currency:$span_id:$class:false|unescape}{if $secondary_currency != $primary_currency}&nbsp;{if $class}<span class="{$class}">{/if}({if $class}</span>{/if}{$value|format_price:$currencies.$secondary_currency:$span_id:$class:true:$is_integer}{if $class}<span class="{$class}">{/if}){if $class}</span>{/if}{/if}
{else}
	{$value|format_price:$currencies.$secondary_currency:$span_id:$class:true|unescape}
{/if}
{/strip}