<!-- [start center] -->
<form action="?mode=save" method="POST">
<input type="hidden" name="form_id" value="debug">
<table class="wm_admin_center" width="500">
	<tr>
		<td width="60"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2" class="wm_admin_title">Debug Settings</td>
	</tr>
	<tr><td colspan="2"><br /></td></tr>
	<tr>
		<td  align="right">
			<input type="checkbox" name="intEnableLogging" onchange="change();" id="intEnableLogging" <?php echo ((bool) $settings->EnableLogging) ? 'checked="checked"' : '';?> value="1" />
		</td>
		<td><label for="intEnableLogging">Enable logging</label></td>
	</tr>
	<tr><td colspan="2">
<div class="wm_safety_info">
<b>Enable logging</b> - enables detailed logging helpful for troubleshooting.
</div><br />
	</td></tr>
	<tr>
		<td></td>
		<td>&nbsp;Path for log&nbsp;&nbsp;<input type="text" name="txtPathForLog" onchange="change();" value="<?php echo ConvertUtils::AttributeQuote(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME);?>" class="wm_input" readonly="readonly" style="width: 330px">
		<br /><br />
<div class="wm_safety_info">
<b>Path for log</b> - path to the log file (cannot be changed).<br />The buttons below allow viewing and clearing the log file.
</div>
		<br />
			<input type="button" onclick="PopUpWindow('?mode=showlog&t=0');" value="View entire log (<?php echo (file_exists(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME)) ? GetFriendlySize(filesize(INI_DIR.'/'.LOG_PATH.'/'.LOG_FILENAME)) : '0KB' ;?>)" class="wm_button" style="font-size: 11px; width: 150px" />
			<input type="button" onclick="PopUpWindow('?mode=showlog&t=1');" value="View last 50KB of log" class="wm_button" style="font-size: 11px; width: 150px" />
			<input type="button" onclick="document.location.replace('?mode=clearlog');" value=" Clear log " class="wm_button" style="font-size: 11px;" />
		</td>
	</tr>
	<tr>
		<td colspan="3" align="center">
			<br /><div id="messDiv" class="messdiv" <?php echo (strlen($divMessage) > 0) ? 'style="border: 1px solid Silver;"' : '';?>>
			<?php echo (strlen($divMessage) > 0)? $divMessage : '&nbsp;';?></div>
		</td>
	</tr>
	<!-- hr -->
	<tr><td colspan="2"><hr size="1"></td></tr>
	<tr>
		<td colspan="2" align="right">
			<input type="submit" name="submit" value="Save" class="wm_button" style="width: 100px">&nbsp;
		</td>
	</tr>
</table>
</form>
<!-- [end center] -->