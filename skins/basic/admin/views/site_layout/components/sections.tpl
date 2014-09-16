{* $Id: sections.tpl 9547 2010-05-20 12:27:21Z klerik $ *}
{if $section_cols}
<table cellpadding="5" cellspacing="1" border="0" width="90%" align="center" class="notification-border-n" id="notification_{$key}">
<tr>
	<td class="notification-body-n">
   		<table cellpadding="5" cellspacing="0" border="0" width="90%" align="center">
   		<tr valign="top">
   			{foreach from=$section_cols item="column"}
   			<td width="33%" class="nowrap">
   			{foreach from=$column item="s"}
   			<p><span class="bull">&bull;</span>
   			{if $s.section_id == $section_id}
				{if $mode == "translate"}
		   			<input class="input-text" type="text" name="translate_sections[{$s.section_id}]" value="{$s.description}" />
					<strong>{$lang.open}</strong>&nbsp;&nbsp;
				{else}
		   			<strong>{$s.description}</strong>
				{/if}
   			{else}
				{if $mode == "translate"}
		   			<input class="input-text" type="text" name="translate_sections[{$s.section_id}]" value="{$s.description}" />
					<a href="{"`$controller`.translate?section_id=`$s.section_id`"|fn_url}" class="underlined">{$lang.view}</a>&nbsp;&nbsp;
				{else}
		   			<a href="{"`$controller`.manage?section_id=`$s.section_id`"|fn_url}">{$s.description}</a>
				{/if}
   			{/if}</p>
   			{/foreach}
   			</td>
   			{/foreach}
   		</tr>
   		</table>
	</td>
</tr>
</table>
{/if}
