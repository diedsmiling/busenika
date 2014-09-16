{* $Id: selectable_box.tpl 7959 2009-09-08 13:01:16Z alexions $ *}

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="50%" valign="top">
		<p align="center"><strong>{$lang.selected_fields}</strong></p>
		<label for="left_{$id}" class="cm-all hidden"></label>
		<select class="input-text cm-expanded" id="left_{$id}" name="addon_data[options][{$name}][]" multiple="multiple" size="10" >
		{if $selected_fields|is_array}
		{foreach from=$selected_fields item="active" key="field_id"}
			<option value="{$field_id}">{$fields.$field_id}</option>
		{/foreach}
		{/if}
		</select>
		<p>
		<img src="{$images_dir}/icons/up_icon.gif" width="11" height="11" onclick="$('#left_{$id}').swapOptions('up');" class="hand" />&nbsp;&nbsp;&nbsp;
		<img src="{$images_dir}/icons/down_icon.gif" width="11" height="11" onclick="$('#left_{$id}').swapOptions('down');" class="hand" />
		</p>

	</td>
	<td class="center valign" width="4%">
		<p><img src="{$images_dir}/icons/to_left_icon.gif" width="11" height="11" onclick="$('#right_{$id}').moveOptions('#left_{$id}');" class="hand" /></p>
		<p><img src="{$images_dir}/icons/to_right_icon.gif" width="11" height="11" onclick="$('#left_{$id}').moveOptions('#right_{$id}', {$ldelim}check_required: true, message: window.lang.error_exim_layout_missed_fields{$rdelim});" class="hand" /></p>
	</td>
	<td width="50%" valign="top">
		<p align="center"><strong>{$lang.available_fields}</strong></p>
		<select class="input-text cm-expanded" name="right_{$id}" id="right_{$id}" multiple="multiple" size="10">
		{foreach from=$fields item="field_name" key="field_id"}
			{if !$selected_fields.$field_id}
				<option value="{$field_id}">{$field_name}</option>
			{/if}
		{/foreach}
		</select>
	</td>
</tr>
</table>