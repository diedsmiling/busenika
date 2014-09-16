{* $Id: manage.tpl 9517 2010-05-19 14:02:43Z klerik $ *}

{include file="views/profiles/components/profiles_scripts.tpl" states=$smarty.const.CART_LANGUAGE|fn_get_all_states:false:true}

{** include fileuploader **}
{include file="common_templates/file_browser.tpl"}
{** /include fileuploader **}


{include file="views/site_layout/components/sections.tpl"}

{capture name="mainbox"}
<form name="settingsform" action="{""|fn_url}" method="post">
<input type="hidden" name="section_id" value="{$section_id}" />
<input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />

<p><strong>Place options in this section to the global scope</strong>&nbsp;<input type="checkbox" name="set_global_section" value="Y" /></p>
<p><strong>Remove options in this section from the global scope</strong>&nbsp;<input type="checkbox" name="rem_global_section" value="Y" /></p>

{capture name="tabsbox"}

<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table">
<tr>
	<th class="center">X</td>
	<th>Pos.</td>
	<th width="100%">Description</td>
	<th>Internal name</td>
	<th>subsection_id</td>
	<th>Value</td>
	<th>Parameters</td>
</tr>
{foreach from=$options item="subsection" key="ukey"}
<tbody id="content_{$ukey}" valign="top">
{foreach from=$subsection item="item"}
	{if $item.element_type == "D"}
		<tr>
			<td class="center"><input name="delete_elm[{$item.element_id}]" type="checkbox" value="1" class="checkbox" /></td>
			<td><input type="text" name="elms_data[{$item.element_id}][position]" size="3" value="{$item.position}" class="input-text-short" /></td>
			<td colspan="5"><hr width="100%"></td>
		</tr>
	{elseif $item.element_type == "I"}
		<tr>
			<td class="center"><input name="delete_elm[{$item.element_id}]" type="checkbox" value="1" class="checkbox" /></td>
			<td><input type="text" name="elms_data[{$item.element_id}][position]" size="3" value="{$item.position}" class="input-text-short" /></td>
			<td colspan="4">{$item.info}</td>
			<td colspan="4">Handler: <input type="text" name="elms_data[{$item.element_id}][handler]" size="20" value="{$item.handler}" /></td>
		</tr>
	{elseif $item.element_type == "H"}
		<tr>
			<td class="center"><input name="delete_elm[{$item.element_id}]" type="checkbox" value="1" class="checkbox" /></td>
			<td><input type="text" name="elms_data[{$item.element_id}][position]" size="3" value="{$item.position}" class="input-text-short" /></td>
			<td colspan="5"><strong>{$item.description}</strong><input type="text" name="descr_elm[{$item.element_id}]" value="{$item.description}" /></td>
		</tr>
	{/if}

	{if !$item.element_type}
	<tr {cycle values="class=\"table-row\", "}>
		<td class="center"><input name="delete[{$item.option_id}]" type="checkbox" value="1" class="checkbox" /></td>
		<td class="section-body"><input type="text" name="position[{$item.option_id}]" size="3" value="{$item.position}" /></td>

		<td>
		<input type="text" name="dev_descr[{$item.option_id}]" value="{$item.description}" />
		<p>{$lang.tooltip}:
		<textarea name="dev_tooltip[{$item.option_id}]" rows="5" cols="19" class="input-text">{$item.tooltip}</textarea></p>
		</td>
		<td><input type="text" name="dev_option_name[{$item.option_id}]" value="{$item.option_name}" /></td>
		<td><select name="dev_subsection[{$item.option_id}]" style="width: 100px;">
				<option value="">--none--</option>
				{foreach from=$subsections item=subsection_inner key=skey}
					<option {if $skey == $item.subsection_id}selected="selected"{/if} value="{$skey}">{$subsection_inner.description}</option>
				{/foreach}
			</select>
		</td>
		<td>
		{if $item.option_type == "P"}
			<input name="update[{$item.option_id}]" type="password" size="20" value="{$item.value}" />
		{elseif $item.option_type == "T"}
			<textarea name="update[{$item.option_id}]" rows="5" cols="19">{$item.value}</textarea>
		{elseif $item.option_type == "C"}
			<input type="hidden" name="update[{$item.option_id}]" value="N" />
			<input type="checkbox" name="update[{$item.option_id}]" value="Y" {if $item.value == "Y"}checked="checked"{/if} />
		{elseif $item.option_type eq "S"}
			<select name="update[{$item.option_id}]" style="width: 100px;">
			{foreach from=$item.variants item=v key=k}
			<option value="{$k}" {if $item.value eq $k}selected="selected"{/if}>{$v} ({$k})</option>
			{/foreach}
			</select>
		{elseif $item.option_type == "R"}
			{foreach from=$item.variants item=v key=k}
			{$v} ({$k})<input type="radio" name="update[{$item.option_id}]" value="{$k}" {if $item.value eq $k}checked{/if} />&nbsp;
			{/foreach}
		{elseif $item.option_type == "M"}
			<select name="update[{$item.option_id}][]" multiple="multiple" style="width: 100px;">
			{foreach from=$item.variants item=v key="k"}
			<option value="{$k}" {if $item.value.$k == "Y"} selected="selected"{/if}>{$v} ({$k})</option>
			{/foreach}
			</select>
		{elseif $item.option_type == "N"}
			{foreach from=$item.variants item=v key="k"}
			{$v} ({$k})<input type="checkbox" name="update[{$item.option_id}][]" value="{$k}" {if $item.value.$k == "Y"} checked="checked"{/if} />&nbsp;
			{/foreach}
		{elseif $item.option_type == "X"}
			<label for="elm_country" class="cm-country cm-location-billing">{$lang.countries_list}:</label>
			<select id="elm_country" name="update[{$item.option_id}]">
				<option value="">- {$lang.select_country} -</option>
				{assign var="countries" value=""|fn_get_simple_countries}
				{foreach from=$countries item=country key=ccode}
				<option value="{$ccode}" {if $ccode == $item.value}selected="selected"{/if}>{$country}</option>
				{/foreach}
			</select>
		{elseif $item.option_type == "W"}
			<script type="text/javascript">
				//<![CDATA[
				var default_state = {$ldelim}'billing':'{$item.value|escape:javascript}'{$rdelim};
				//]]>
			</script>
			<label for="elm_state" class="cm-state cm-location-billing">{$lang.states_list}:</label>
			<input type="text" id="elm_state_d" name="update[{$item.option_id}]" size="32" maxlength="64" value="" value="{$item.value}" disabled="disabled" class="hidden" />
			<select id="elm_state" name="update[{$item.option_id}]" style="width: 100px;">
				<option value="">- {$lang.select_state} -</option>
			</select>
		{elseif $item.option_type == "F"}
				<input type="text" id="input_button_server_{$item.option_id}" name="update[{$item.option_id}]" value="{$item.value}" size="20" class="valign input-text" readonly="readonly" /><input id="button_server_{$item.option_id}" type="button" value="{$lang.server|escape:html}..." class="valign input-text" onclick="fileuploader.init('box_server_upload', 'input_' + this.id, event);" />
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
			<input name="update[{$item.option_id}]" type="text" size="20" value="{$item.value}" />
		{/if}
		</td>
		<td>
		{if ($item.option_type == "S" || $item.option_type == "R" || $item.option_type == "M" || $item.option_type == "N") && !$item.userfunc}
			<table>
			{foreach from=$item.variants item=v key=k}
			<tr valign="top" class="cm-first-sibling">
				<td><input type="checkbox" size="8" name="dev_option_variants[{$item.option_id}][{$item.dev_variants.$k.variant_id}][delete]" value="Y" class="checkbox" /></td>
				<td><input type="text" size="8" name="dev_option_variants[{$item.option_id}][{$item.dev_variants.$k.variant_id}][vname]" value="{$k}" /></td>
				<td><input type="text" size="8" name="dev_option_variants[{$item.option_id}][{$item.dev_variants.$k.variant_id}][vdesc]" value="{$v}" /></td>
				<td><input type="text" size="2" name="dev_option_variants[{$item.option_id}][{$item.dev_variants.$k.variant_id}][vpos]" value="{$item.dev_variants.$k.position}" /></td>
				<td>&nbsp;</td>
			</tr>
			{/foreach}
			<tr valign="top" id="box_option_vars_{$item.option_id}_tag">
				<td>&nbsp;</td>
				<td><input type="text" size="8" name="dev_add_option_variants[{$item.option_id}][0][vname]" value="" /></td>
				<td><input type="text" size="8" name="dev_add_option_variants[{$item.option_id}][0][vdesc]" value="" /></td>
				<td><input type="text" size="2" name="dev_add_option_variants[{$item.option_id}][0][vpos]" value="" /></td>
				<td>{include file="buttons/multiple_buttons.tpl" tag_level="2" item_id="option_vars_`$item.option_id`_tag"}</td>
			</tr>
			</table>
		{/if}

		{capture name="fa"}fn_settings_actions_{$section_id|lower}_{if $item.subsection_id}{$item.subsection_id}_{/if}{$item.option_name}{/capture}
		{capture name="fv"}fn_settings_variants_{$section_id|lower}_{if $item.subsection_id}{$item.subsection_id}_{/if}{$item.option_name}{/capture}

		<p>Action func:{if $smarty.capture.fa|function_exists}<br /><b>{$smarty.capture.fa}</b>{else}-{/if}</p>
		<p>Variants func:{if $smarty.capture.fv|function_exists}<br /><b>{$smarty.capture.fv}</b>{else}-{/if}</p>
		<p>Global: &nbsp;<input type="hidden" name="is_global[{$item.option_id}]" value="N"><input type="checkbox" name="is_global[{$item.option_id}]" value="Y" {if $item.is_global == "Y"}checked="checked"{/if} /></p>
		Option type: &nbsp;
				<select name="dev_option_type[{$item.option_id}]">
					<option value="I" {if $item.option_type == "I"}selected="selected"{/if}>Text input</option>
					<option value="P" {if $item.option_type == "P"}selected="selected"{/if}>Password input</option>
					<option value="C" {if $item.option_type == "C"}selected="selected"{/if}>Checkbox</option>
					<option value="T" {if $item.option_type == "T"}selected="selected"{/if}>Textarea</option>
					<option value="R" {if $item.option_type == "R"}selected="selected"{/if}>Radiogroup</option>
					<option value="S" {if $item.option_type == "S"}selected="selected"{/if}>Selectbox</option>
					<option value="M" {if $item.option_type == "M"}selected="selected"{/if}>MultipleBox</option>
					<option value="N" {if $item.option_type == "N"}selected="selected"{/if}>MultipleCheckBox</option>
					<option value="F" {if $item.option_type == "F"}selected="selected"{/if}>Select file on server</option>
					<option value="W" {if $item.option_type == "W"}selected="selected"{/if}>{$lang.states_list}</option>
					<option value="X" {if $item.option_type == "X"}selected="selected"{/if}>{$lang.countries_list}</option>
					<option value="G" {if $item.option_type == "G"}selected="selected"{/if}>{$lang.combo_checkboxes}</option>
					<option value="K" {if $item.option_type == "K"}selected="selected"{/if}>{$lang.combo_selectbox}</option>
				</select>
		</td>
	</tr>
	{/if}
{/foreach}
</tbody>
{/foreach}
</table>

<div class="buttons-container buttons-bg">
	<input type="submit" name="dispatch[settings_dev.update]" value="{$lang.save|escape:html}" />
</div>
</form>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox group_name=$controller}

{/capture}
{include file="common_templates/mainbox.tpl" title=$sections.$section_id.description content=$smarty.capture.mainbox}

{literal}
<script type="text/javascript">
//<![CDATA[
function dev_check(val)
{
 if ((val=='R')||(val=='S')||(val=='M')||(val=='N')) {
	document.getElementById('option_variants').disabled = false;
 } else {
	document.getElementById('option_variants').disabled = true;
 }
}
//]]>
</script>
{/literal}

{capture name="mainbox"}
<table>
<tr>
<td>
	<form name="devoptionform" action="{""|fn_url}" method="post">
	<input name="section_id" type="hidden" value="{$section_id}" />
	<input type="hidden" id="ss2" name="selected_section" value="" />
	<p><strong>Add new option:</strong></p>
	<table>
	<tr>
		<td>Section ID</td>
		<td>
			<select name="dev[section_id]">
				<option value="">--none--</option>
			{foreach from=$sections item=sct key=skey}
				<option value="{$skey}" {if $skey == $section_id}selected="selected"{/if}>{$sct.description}</option>
			{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>subsection ID</td>
		<td>
			<select id="subs" name="dev[subsection_id]">
				<option value="">--none--</option>
			{foreach from=$subsections item=subsection key=skey}
				<option value="{$skey}">{$subsection.description}</option>
			{/foreach}
			</select>
			<script type="text/javascript">
				//<![CDATA[
				sb = document.getElementById('subs');
				sb.value = document.getElementById('selected_section').value;
				document.getElementById('ss2').value = sb.value;
				//]]>
			</script>
		</td>
	</tr>
	<tr>
		<td>option name</td>
		<td><input type="text" name="dev[option_name]" value="" /></td>
	</tr>
	<tr>
		<td>option description</td>
		<td><input type="text" name="dev[description]" value="" /></td>
	</tr>
	<tr>
		<td>value</td>
		<td><input type="text" name="dev[value]" value="" /></td>
	</tr>
	<tr>
		<td>Position</td>
		<td><input type="text" size="3" name="dev[position]" value="" /></td>
	</tr>
	<tr>
		<td>Type</td>
		<td><select name="dev[option_type]" onChange="javascript: dev_check(this.value);">
				<option value="I">Text input</option>
				<option value="P">Password input</option>
				<option value="C">Checkbox</option>
				<option value="T">Textarea</option>
				<option value="R">Radiogroup</option>
				<option value="S">Selectbox</option>
				<option value="M">MultipleBox</option>
				<option value="N">MultipleCheckBox</option>
				<option value="F">Select file on server</option>
				<option value="W">{$lang.states_list}</option>
				<option value="X">{$lang.countries_list}</option>
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top">Option value variants:</td>
		<td>
		<table cellpadding="0" cellspacing="1" border="0" id="option_variants">
		<tr valign="top" class="cm-first-sibling">
			<td nowrap><strong>Name</strong></td>
			<td nowrap><strong>Descr</strong></td>
			<td nowrap><strong>Pos</strong></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr valign="top" id="box_option_variants_tag">
			<td nowrap><input type="text" size="8" name="dev_add_ovars[0][vname]" value="" /></td>
			<td nowrap><input type="text" size="8" name="dev_add_ovars[0][vdesc]" value="" /></td>
			<td nowrap><input type="text" size="2" name="dev_add_ovars[0][vpos]" value="" /></td>
			<td>{include file="buttons/multiple_buttons.tpl" item_id="option_variants_tag"}</td>
		</tr>
		</table>
		&nbsp;

		</td>
	</tr>
	</table>
	<input type="submit" value="submit" name="dispatch[settings_dev.dev_add_option]" />
	</form>

</td>
<td valign="top">
<table>
<tr>
	<td valign="top">
		<form name="devsectionform" action="{""|fn_url}" method="post">
		<input name="section_id" type="hidden" value="{$section_id}" />

		<p><strong>Add new section:</strong></p>
		<table>
		<tr>
			<td>section_id</td>
			<td><input type="text" name="dev[section_id]" value="" /></td>
		</tr>
		<tr>
			<td>section description</td>
			<td><input type="text" name="dev[description]" value="" /></td>
		</tr>
		<tr>
			<td>section position</td>
			<td><input type="text" size="3" name="dev[position]" value="" /></td>
		</tr>
		</table>
		<input type="submit" value="Submit" name="dispatch[settings_dev.dev_add_section]" />
		</form>
	</td>
	<td valign="top">
		<form name="devsubsectionform" action="{""|fn_url}" method="post">
		<input name="section_id" type="hidden" value="{$section_id}" />
		<input type="hidden" name="dev[section_id]" value="{$section_id}" />

		<p><strong>Add new subsection to current section:</strong></p>
		<table>
		<tr>
			<td>subsection_id</td>
			<td><input type="text" name="dev[subsection_id]" value="" /></td>
		</tr>
		<tr>
			<td>subsection description</td>
			<td><input type="text" name="dev[description]" value="" /></td>
		</tr>
		<tr>
			<td>subsection position</td>
			<td><input type="text" size="3" name="dev[position]" value="" /></td>
		</tr>
		</table>
		<input type="submit" value="submit" name="dispatch[settings_dev.dev_add_subsection]" />
		</form>
	</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td colspan="2">
		<form name="develementsform" action="{""|fn_url}" method="post">
		<input name="section_id" type="hidden" value="{$section_id}" />
		<input type="hidden" name="dev[section_id]" value="{$section_id}" />
		<input type="hidden" id="ss3" name="selected_section" value="" />

		<p><strong>Add additional elements:</strong></p>
		<table>
		<tr>
			<td>subsection ID</td>
			<td>
				<select id="subs2" name="dev[subsection_id]">
					<option value="">--none--</option>
				{foreach from=$subsections item=subsection key=skey}
					<option value="{$skey}">{$subsection.description}</option>
				{/foreach}
				</select>
				<script type="text/javascript">
					//<![CDATA[
					sb = document.getElementById('subs2');
					sb.value = document.getElementById('selected_section').value;
					document.getElementById('ss3').value = sb.value;
					//]]>
				</script>
			</td>
		</tr>
		<tr>
			<td>Type</td>
			<td><select name="dev[element_type]">
					<option value="D">Separator</option>
					<option value="H">Header</option>
					<option value="I">Info field</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>description (for header)</td>
			<td><input type="text" name="dev[description]" value="" /></td>
		</tr>
		<tr>
			<td>position</td>
			<td><input type="text" size="3" name="dev[position]" value="" /></td>
		</tr>
		<tr>
			<td>Handler (for info field)</td>
			<td><input type="text" name="dev[handler]" value="" /></td>
		</tr>
		</table>
		<input type="submit" value="submit" name="dispatch[settings_dev.dev_add_element]" />
		</form>
	</td>
</tr>
</table>
</td>
</tr>
</table>

{/capture}
{include file="common_templates/mainbox.tpl" title="Development console" content=$smarty.capture.mainbox}
