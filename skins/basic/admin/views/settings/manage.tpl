{* $Id: manage.tpl 10293 2010-08-02 11:02:07Z klerik $ *}

{include file="views/profiles/components/profiles_scripts.tpl" states=$smarty.const.CART_LANGUAGE|fn_get_all_states:false:true}

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}

{if $smarty.request.highlight}
{assign var="highlight" value=","|explode:$smarty.request.highlight}
{/if}

<form action="{""|fn_url}" method="post" name="settings_form" class="cm-form-highlight">
<input name="section_id" type="hidden" value="{$section_id}" />
<input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />

{capture name="mainbox"}

{capture name="tabsbox"}

{foreach from=$options item=subsection key="ukey"}
<div id="content_{$ukey}">
<table cellpadding="0" cellspacing="0" border="0" class="settings" width="100%">
{foreach from=$subsection item=item name="section"}
{if $item.element_type == "D"}
	<tr>
		<td colspan="2"><hr width="100%" /></td>
	</tr>
{elseif $item.element_type == "I"}
	<tr>
		<td colspan="2">{$item.info|unescape}</td>
	</tr>
{elseif $item.element_type == "H"}
	<tr>
		<td colspan="2">
			{include file="common_templates/subheader.tpl" title=$item.description}
		</td>
	</tr>
{/if}

{if !$item.element_type}
<tr class="form-field">
	<td width="60%">
		<label for="elm_{$item.option_id}" class="description {if $highlight && $item.option_name|in_array:$highlight}highlight{/if} {if $item.option_type == "X"}cm-country cm-location-billing{elseif $item.option_type == "W"}cm-state cm-location-billing{/if}">
		{$item.description|unescape}{if $item.tooltip}{capture name="tooltip"}{$item.tooltip}{/capture}{include file="common_templates/tooltip.tpl" tooltip=$smarty.capture.tooltip}{/if}
		</label>
	</td>
	<td class="nowrap" width="40%">
	{if $item.option_type == "P"}
		<input id="elm_{$item.option_id}" type="password" name="update[{$item.option_id}]" size="30" value="{$item.value}" class="input-text" />
	{elseif $item.option_type == "T"}
		<textarea id="elm_{$item.option_id}" name="update[{$item.option_id}]" rows="5" cols="19" class="input-text">{$item.value}</textarea>
	{elseif $item.option_type == "C"}
		<input type="hidden" name="update[{$item.option_id}]" value="N" />
		<input id="elm_{$item.option_id}" type="checkbox" name="update[{$item.option_id}]" value="Y" {if $item.value == "Y"}checked="checked"{/if} class="checkbox" />
	{elseif $item.option_type == "S"}
		<select id="elm_{$item.option_id}" name="update[{$item.option_id}]">
			{foreach from=$item.variants item=v key=k}
				<option value="{$k}" {if $item.value == $k}selected="selected"{/if}>{$v}</option>
			{/foreach}
		</select>
	{elseif $item.option_type == "R"}
		<div class="select-field">
		{foreach from=$item.variants item=v key=k}
		<input type="radio" name="update[{$item.option_id}]" value="{$k}" {if $item.value == $k}checked="checked"{/if} class="radio" id="variant_{$item.description|md5}_{$k|md5}" />&nbsp;<label for="variant_{$item.description|md5}_{$k|md5}">{$v}</label>
		{/foreach}
		</div>
	{elseif $item.option_type == "M"}
		<select id="elm_{$item.option_id}" name="update[{$item.option_id}][]" multiple="multiple">
		{foreach from=$item.variants item=v key="k"}
		<option value="{$k}" {if $item.value.$k == "Y"}selected="selected"{/if}>{$v}</option>
		{/foreach}
		</select>
		{$lang.multiple_selectbox_notice}
	{elseif $item.option_type == "N"}
		<div class="select-field">
		{foreach from=$item.variants item=v key="k"}
		<input type="checkbox" name="update[{$item.option_id}][]" id="variant_{$item.description|md5}_{$k|md5}" value="{$k}" {if $item.value.$k == "Y"}checked="checked"{/if} />&nbsp;<label for="variant_{$item.description|md5}_{$k|md5}">{$v}</label>
		{/foreach}
		</div>
	{elseif $item.option_type == "X"}
		<select id="elm_{$item.option_id}" name="update[{$item.option_id}]">
			<option value="">- {$lang.select_country} -</option>
			{assign var="countries" value=""|fn_get_simple_countries}
			{foreach from=$countries item=country key=ccode}
				<option value="{$ccode}" {if $ccode == $item.value}selected="selected"{/if}>{$country|escape}</option>
			{/foreach}
		</select>
	{elseif $item.option_type == "W"}
		<script type="text/javascript">
			//<![CDATA[
			var default_state = {$ldelim}'billing':'{$item.value|escape:javascript}'{$rdelim};
			//]]>
		</script>
		<input type="text" id="elm_{$item.option_id}_d" name="update[{$item.option_id}]" value="{$item.value}" size="32" maxlength="64" disabled="disabled" class="hidden input-text" />
		<select id="elm_{$item.option_id}" name="update[{$item.option_id}]">
			<option value="">- {$lang.select_state} -</option>
		</select>
	{elseif $item.option_type == "F"}
		<input id="file_elm_{$item.option_id}" type="text" name="update[{$item.option_id}]" value="{$item.value}" size="30" class="valign input-text" />&nbsp;<input id="elm_{$item.option_id}" type="button" value="{$lang.browse}" class="valign input-text" onclick="fileuploader.init('box_server_upload', this.id);" />
	{elseif $item.option_type == "G"}
		<div class="table-filters">
			<div class="scroll-y">
				{foreach from=$item.variants item=v key="k"}
					<div class="select-field"><input type="checkbox" class="checkbox cm-combo-checkbox" id="option_{$k}" name="update[{$item.option_id}][]" value="{$k}" {if $item.value.$k == "Y"}checked="checked"{/if} /><label for="option_{$k}">{$v}</label></div>
				{/foreach}
			</div>
		</div>
	{elseif $item.option_type == "K"}
		<select id="elm_{$item.option_id}" name="update[{$item.option_id}]" class="cm-combo-select">
			{foreach from=$item.variants item=v key=k}
				<option value="{$k}" {if $item.value == $k}selected="selected"{/if}>{$v}</option>
			{/foreach}
		</select>
	{else}
		<input id="elm_{$item.option_id}" type="text" name="update[{$item.option_id}]" size="30" value="{$item.value}" class="input-text" />
	{/if}
	</td>
</tr>
{/if}
{/foreach}
</table>
</div>
{/foreach}

<div class="buttons-container buttons-bg">
	{include file="buttons/save.tpl" but_name="dispatch[settings.update]" but_role="button_main"}
</div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox track=true}

{/capture}

{include file="common_templates/mainbox.tpl" title="`$lang.settings`: `$settings_title`" content=$smarty.capture.mainbox}

</form>

