{* $Id: selectboxes.tpl 9781 2010-06-10 08:03:42Z zeke $ *}

<p>{$lang.text_exim_export_notice}</p>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="50%" valign="top">
		<p align="center"><label for="{$left_id}" class="cm-required cm-all"><strong>{$lang.exported_fields}</strong></label></p>
		<select class="input-text cm-expanded" id="{$left_id}" name="{$left_name}[]" multiple="multiple" size="10" >
		{foreach from=$assigned_ids item=key}
		{if $items.$key}
		<option value="{$key}" {if $items.$key.required}class="selectbox-highlighted cm-required"{/if}>{$key}</option>
		{/if}
		{/foreach}

		{foreach from=$items item="item" key="key"}
		{if $item.required && !$key|in_array:$assigned_ids}
		<option value="{$key}" class="selectbox-highlighted cm-required">{$key}</option>
		{/if}
		{/foreach}

		</select>
		<p>
		<img src="{$images_dir}/icons/up_icon.gif" width="11" height="11" onclick="$('#{$left_id}').swapOptions('up');" class="hand" />&nbsp;&nbsp;&nbsp;
		<img src="{$images_dir}/icons/down_icon.gif" width="11" height="11" onclick="$('#{$left_id}').swapOptions('down');" class="hand" />
		</p>

	</td>
	<td class="center valign" width="4%">
		<p><img src="{$images_dir}/icons/to_left_icon.gif" width="11" height="11" onclick="$('#{$left_id}_right').moveOptions('#{$left_id}');" class="hand" /></p>
		<p><img src="{$images_dir}/icons/to_right_icon.gif" width="11" height="11" onclick="$('#{$left_id}').moveOptions('#{$left_id}_right', {$ldelim}check_required: true, message: window.lang.error_exim_layout_missed_fields{$rdelim});" class="hand" /></p>
	</td>
	<td width="50%" valign="top">
		<p align="center"><label for="{$left_id}_right"><strong>{$lang.available_fields}</strong></label></p>
		<select class="input-text cm-expanded" id="{$left_id}_right" name="unset_mbox[]" multiple="multiple" size="10" >
		{foreach from=$items item=item key=key}
		{if !$key|in_array:$assigned_ids && !$item.required}
		<option value="{$key}" {if $item.required}class="selectbox-highlighted cm-required"{/if}>{$key}</option>
		{/if}
		{/foreach}
		</select>
	</td>
</tr>
</table>
