{* $Id: price.tpl 5807 2008-08-26 09:27:03Z zeke $ *}
{strip}
{if $settings.General.alternative_currency == "Y"}
	{$value|format_price:$currencies.$primary_currency:$span_id:$class}{if $secondary_currency != $primary_currency}&nbsp;({$value|format_price:$currencies.$secondary_currency:$span_id:$class:true}){/if}
{else}
	{$value|format_price:$currencies.$secondary_currency:$span_id:$class:true}
{/if}
{/strip}