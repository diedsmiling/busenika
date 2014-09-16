{* $Id: select.tpl 10293 2010-08-02 11:02:07Z klerik $ *}
{assign var="data" value=$data_from|fn_explode_localizations}

{if $localizations}
{if !$no_div}
<div class="form-field">
	<label for="{$id}">{$lang.localization}:</label>
{/if}
		{if !$disabled}<input type="hidden" name="{$data_name}" value="" />{/if}
		<select	name="{$data_name}[]" multiple="multiple" size="3" id="{$id|default:$data_name}" class="{if $disabled}elm-disabled{else}input-text{/if}" {if $disabled}disabled="disabled"{/if}>
			{foreach from=$localizations item="loc"}
			<option	value="{$loc.localization_id}" {foreach from=$data item="p_loc"}{if $p_loc == $loc.localization_id}selected="selected"{/if}{/foreach}>{$loc.localization|escape}</option>
			{/foreach}
		</select>
{if !$no_div}
{$lang.multiple_selectbox_notice}
</div>
{/if}
{/if}
