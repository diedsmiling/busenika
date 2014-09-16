{* $Id: double_selectboxes.tpl 7249 2009-04-13 08:25:18Z zeke $ *}

{assign var="first_id" value=$first_name|md5}
{assign var="second_id" value=$second_name|md5}

{notes unique=true}
	{$lang.multiple_selectbox_notice}
{/notes}

<table cellpadding="0" cellspacing="0" width="100%"	border="0">
<tr>
	<td colspan="4">{include file="common_templates/subheader.tpl" title=$title}</td>
</tr>
<tr>
	<td width="48%">
		<p><label for="id_{$first_id}" class="cm-required cm-all hidden"></label>
		<select name="{$first_name}[]" id="id_{$first_id}" size="10" value="" multiple="multiple" class="input-text cm-expanded cm-required">
			{foreach from=$first_data key=key item=value}
				<option	value="{$key}">{$value}</option>
			{/foreach}
		</select></p>
		<p>
		<img src="{$images_dir}/icons/up_icon.gif" width="11" height="11" onclick="$('#id_{$first_id}').swapOptions('up');" class="hand" />&nbsp;&nbsp;&nbsp;
		<img src="{$images_dir}/icons/down_icon.gif" width="11" height="11" onclick="$('#id_{$first_id}').swapOptions('down');" class="hand" />
		</p>
	</td>
	<td class="center valign" width="4%">
		<p><img src="{$images_dir}/icons/to_left_icon.gif" width="11" height="11" onclick="$('#id_{$second_id}').moveOptions('#id_{$first_id}');" class="hand" /></p>
		<p><img src="{$images_dir}/icons/to_right_icon.gif" width="11" height="11" onclick="$('#id_{$first_id}').moveOptions('#id_{$second_id}');" class="hand" /></p>
	</td>
	<td width="48%" valign="top">
		<p><select name="{$second_name}" id="id_{$second_id}" size="10" value="" multiple="multiple" class="input-text cm-expanded">
			{foreach from=$second_data key=key item=value}
				<option	value="{$key}">{$value}</option>
			{/foreach}
		</select></p>
	</td>
</tr>
</table>