{* $Id: chmod.tpl 7497 2009-05-19 10:41:21Z zeke $ *}

<div id="template_editor_perms">
	<div class="object-container" align="center">
		<table cellspacing="1" class="center">
		<tr>
			<td class="manage-row" colspan="3" >{$lang.owner}</td>
			<td colspan="3" >{$lang.group}</td>
			<td class="manage-row" colspan="3" >{$lang.world}</td>
		</tr>
		<tr>
			<td><strong>r</strong></td>
			<td><strong>w</strong></td>
			<td><strong>x</strong></td>
		
			<td class="manage-row"><strong>r</strong></td>
			<td class="manage-row"><strong>w</strong></td>
			<td class="manage-row"><strong>x</strong></td>
		
			<td><strong>r</strong></td>
			<td><strong>w</strong></td>
			<td><strong>x</strong></td>
		</tr>
		
		<tr>
			<td><input id="o_read" type="checkbox" name="o_read" /></td>
			<td><input id="o_write" type="checkbox" name="o_write" /></td>
			<td><input id="o_exec" type="checkbox" name="o_exec" /></td>
		
			<td><input id="g_read" type="checkbox" name="g_read" /></td>
			<td><input id="g_write" type="checkbox" name="g_write" /></td>
			<td><input id="g_exec" type="checkbox" name="g_exec" /></td>
		
			<td><input id="w_read" type="checkbox" name="w_read" /></td>
			<td><input id="w_write" type="checkbox" name="w_write" /></td>
			<td><input id="w_exec" type="checkbox" name="w_exec" /></td>
		</tr>
		</table>
		
		<div class="center">
			<label for="chmod_recursive">{$lang.recursively}:</label> <input id="chmod_recursive" type="checkbox" name="r" value="Y" />
		</div>
	</div>
	
	<div class="buttons-container">
		{include file="buttons/save_cancel.tpl" but_type="button" but_onclick="template_editor.set_perms()" but_meta="cm-popup-switch" cancel_action="close"}
	</div>
</div>
