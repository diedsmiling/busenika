{* $Id: export.tpl 10055 2010-07-14 10:15:19Z klerik $ *}

{script src="js/picker.js"}

<script type="text/javascript">
//<![CDATA[
	lang.error_exim_layout_missed_fields = '{$lang.error_exim_layout_required_fields|escape:javascript}';
//]]>
</script>

{if $pattern.range_options}
	{assign var="r_opt" value=$pattern.range_options}
	{assign var="r_url" value="exim.export?section=`$pattern.section`&pattern_id=`$pattern.pattern_id`"|fn_url}
	{assign var="oname" value=$r_opt.object_name|lower}
	{notes}
	{if $export_range}
		{$lang.text_objects_for_export|replace:"[total]":$export_range|replace:"[name]":$oname}
		<p>
		<a href="{$r_opt.selector_url|fn_url}">{$lang.change_range} &#155;&#155;</a>&nbsp;&nbsp;&nbsp;<a href="{"exim.delete_range?section=`$pattern.section`&amp;pattern_id=`$pattern.pattern_id`"|fn_url}">{$lang.delete_range} &#155;&#155;</a>
		</p>
	{else}
		{$lang.text_select_range|replace:"[name]":$oname}: <a href="{"exim.select_range?section=`$pattern.section`&amp;pattern_id=`$pattern.pattern_id`"|fn_url}">{$lang.select} &#155;&#155;</a>
	{/if}
	{/notes}
{/if}

{capture name="mainbox"}

{capture name="tabsbox"}

{assign var="p_id" value=$pattern.pattern_id}
<div id="content_{$p_id}">
	{include file="common_templates/subheader.tpl" title=$lang.general}
	<form action="{""|fn_url}" method="post" name="{$p_id}_set_layout_form">
	<input type="hidden" name="section" value="{$pattern.section}" />
	<input type="hidden" name="layout_data[pattern_id]" value="{$p_id}" />

	{$lang.layouts}:&nbsp;
		{if $layouts}
		<select name="layout_data[layout_id]" id="s_layout_id_{$p_id}" class="valign">
		{foreach from=$layouts item=l}
			<option value="{$l.layout_id}" {if $l.active == "Y"}{assign var="active_layout" value=$l}selected="selected"{/if}>{$l.name}</option>
		{/foreach}
		</select>&nbsp;
		{include file="buttons/button.tpl" but_name="dispatch[exim.set_layout]" but_text=$lang.load but_role="submit"}
		{$lang.or}&nbsp;&nbsp;
		<a class="cm-confirm tool-link" href="{"exim.delete_layout?section=`$pattern.section`&amp;pattern_id=`$p_id`"|fn_url}" onclick="this.href += '&layout_id=' + $('#s_layout_id_{$p_id}').val();" class="text-button-edit">{$lang.delete}</a>
		{else}
		<span class="lowercase">{$lang.no_items}</span>
		{/if}
	</form>																									  

	<form action="{""|fn_url}" method="post" name="{$p_id}_manage_layout_form">
	<input type="hidden" name="section" value="{$pattern.section}" />
	<input type="hidden" name="layout_data[pattern_id]" value="{$p_id}" />
	<input type="hidden" name="layout_data[layout_id]" value="{$active_layout.layout_id}" />

	{include file="views/exim/components/selectboxes.tpl" items=$pattern.export_fields assigned_ids=$active_layout.cols left_name="layout_data[cols]" left_id="pattern_`$p_id`" p_id=$p_id}

	<div class="buttons-container right">
		<div class="float-left">
			{include file="buttons/button.tpl" but_name="dispatch[exim.store_layout]" but_text=$lang.save_layout}
			{$lang.or}&nbsp;&nbsp;&nbsp;
			{include file="buttons/button.tpl" but_text=$lang.clear_fields but_onclick="$('#pattern_`$p_id`').moveOptions('#pattern_`$p_id`_right', `$ldelim`move_all: true`$rdelim`);" but_role="edit"}
		</div>
		<label for="layout_data">{$lang.save_layout_as}:</label>
		<input type="text" id="layout_data" class="input-text valign" name="layout_data[name]" value="" />
		{include file="buttons/button.tpl" but_name="dispatch[exim.store_layout/save_as]" but_text=$lang.save}
	</div>

	{include file="common_templates/subheader.tpl" title=$lang.export_options}

	{if $pattern.options}
	{foreach from=$pattern.options key=k item=o}
	{if !$o.import_only}
	<div class="form-field">
		<label for="{$p_id}_{$k}">{$lang[$o.title]}:</label>
		{if $o.type == "checkbox"}
			<input type="hidden" name="export_options[{$k}]" value="N" />
			<input id="{$p_id}_{$k}" class="checkbox" type="checkbox" name="export_options[{$k}]" value="Y" {if $o.default_value == "Y"}checked="checked"{/if} />
		{elseif $o.type == "input"}
			<input id="{$p_id}_{$k}" class="input-text-large" type="text" name="export_options[{$k}]" value="{$o.default_value}" />
		{elseif $o.type == "languages"}
			<select id="{$p_id}_{$k}" name="export_options[{$k}]">
			{foreach from=$languages item=language}
				<option value="{$language.lang_code}" {if $language.lang_code == $smarty.const.CART_LANGUAGE}selected="selected"{/if}>{$language.name}</option>
			{/foreach}
			</select>
		{elseif $o.type == "select"}
			<select id="{$p_id}_{$k}" name="export_options[{$k}]">
			{if $o.variants_function}
				{foreach from=$o.variants_function|call_user_func key=vk item=vi}
				<option value="{$vk}" {if $vk == $o.default_value}checked="checked"{/if}>{$vi}</option>
				{/foreach}
			{else}
				{foreach from=$o.variants key=vk item=vi}
				<option value="{$vk}" {if $vk == $o.default_value}checked="checked"{/if}>{$lang.$vi}</option>
				{/foreach}
			{/if}
			</select>
		{/if}
		{if $o.description}<p class="description">{$lang[$o.description]}</p>{/if}
	</div>
	{/if}
	{/foreach}
	{/if}
	{assign var="override_options" value=$pattern.override_options}
	{if $override_options.delimiter}
		<input type="hidden" name="export_options[delimiter]" value="{$override_options.delimiter}" />
	{else}
	<div class="form-field">
		<label>{$lang.csv_delimiter}:</label>
		{include file="views/exim/components/csv_delimiters.tpl" name="export_options[delimiter]"}
	</div>
	{/if}
	{if $override_options.output}
		<input type="hidden" name="export_options[output]" value="{$override_options.output}" />
	{else}
	<div class="form-field">
		<label for="output">{$lang.output}:</label>
		<select name="export_options[output]" id="output">
			<option value="D">{$lang.direct_download}</option>
			<option value="C">{$lang.screen}</option>
			<option value="S">{$lang.server}</option>
		</select>
	</div>
	{/if}
	<div class="form-field">
		<label for="filename">{$lang.filename}:</label>
		<input type="text" name="export_options[filename]" id="filename" size="50" class="input-text-large" value="{if $pattern.filename}{$pattern.filename}{else}{$p_id}_{$l.name}_{$smarty.const.TIME|date_format:"%m%d%Y"}.csv{/if}" />
		{assign var="filename_description" value=$pattern.filename_description}
		{if $pattern.filename_description}<p class="description">{$lang[$filename_description]}</p>{/if}
	</div>

	<div class="buttons-container buttons-bg">
		{include file="buttons/button.tpl" but_text=$lang.export but_name="dispatch[exim.export]" but_role="button_main"}
	</div>

	</form>
<!--content_{$p_id}--></div>

{/capture}
{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$p_id}

{capture name="extra_tools"}
{capture name="exported_files"}
{assign var="c_url" value=$config.current_url|escape:url}
<div class="object-container" id="content_exported_files">
<table cellpadding="0" cellspacing="0" border="0" class="table" width="100%">
<tr>
	<th width="100%">{$lang.filename}</th>
	<th>{$lang.filesize}</th>
	<th colspan="2">&nbsp;</th>
</tr>
{foreach from=$export_files item=file name="export_files"}
{assign var="file_name" value=$file.name|escape:"url"}
<tr {cycle values="class=\"table-row\", "}>
	<td>
		<a href="{"exim.get_file?filename=`$file_name`"|fn_url}">{$file.name}</a></td>
	<td>
		{$file.size|number_format}&nbsp;{$lang.bytes}</td>
	<td>
		<a href="{"exim.get_file?filename=`$file_name`"|fn_url}" class="underlined"><img src="{$images_dir}/icons/icon_download.gif" width="16" height="16" border="0" alt="{$lang.download}" title="{$lang.download}" /></a>
	</td>
	<td class="nowrap">
		{capture name="tools_items"}
		<li><a class="cm-ajax cm-confirm" href="{"exim.delete_file?filename=`$file_name`&amp;redirect_url=`$c_url`"|fn_url}" rev="content_exported_files">{$lang.delete}</a></li>
		{/capture}
		{include file="common_templates/table_tools_list.tpl" prefix=$smarty.foreach.export_files.iteration tools_list=$smarty.capture.tools_items}
	</td>
</tr>
{foreachelse}
<tr class="no-items">
	<td colspan="4"><p>{$lang.no_data}</p></td>
</tr>
{/foreach}
</table>
<!--content_exported_files--></div>
{/capture}
{include file="common_templates/popupbox.tpl" act="edit" id="exported_files" link_text=$lang.exported_files text=$lang.exported_files content=$smarty.capture.exported_files link_class="tool-link"}
{/capture}

{/capture}
{include file="common_templates/mainbox.tpl" title=$lang.export_data content=$smarty.capture.mainbox extra_tools=$smarty.capture.extra_tools}

{if $smarty.request.output == "D"}
<meta http-equiv="Refresh" content="0;URL={"exim.get_file?filename=`$smarty.request.filename`"|fn_url}" />
{/if}
