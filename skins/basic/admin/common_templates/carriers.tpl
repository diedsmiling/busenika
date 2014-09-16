{* $Id: carriers.tpl 8700 2010-01-27 11:26:42Z alexions $ *}

<select {if $id}id="{$id}"{/if} name="{$name}">
	<option value="">--</option>
	<option value="USP" {if $carrier == "USP"}selected="selected"{/if}>{$lang.usps}</option>
	<option value="UPS" {if $carrier == "UPS"}selected="selected"{/if}>{$lang.ups}</option>
	<option value="FDX" {if $carrier == "FDX"}selected="selected"{/if}>{$lang.fedex}</option>
	<option value="AUP" {if $carrier == "AUP"}selected="selected"{/if}>{$lang.australia_post}</option>
	<option value="DHL" {if $carrier == "DHL" || $user_data.carrier == "ARB"}selected="selected"{/if}>{$lang.dhl}</option>
	<option value="CHP" {if $carrier == "CHP"}selected="selected"{/if}>{$lang.chp}</option>
</select>