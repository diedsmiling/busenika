<!-- [start center] -->
<form action="?mode=save" method="POST">
<input type="hidden" name="form_id" value="interface">
<table class="wm_admin_center" width="500" border="0">
	<tr>
		<td width="150"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2" class="wm_admin_title">Interface Settings</td>
	</tr>
	<tr><td colspan="2"><br /></td></tr>
	<tr>
		<td align="right">Mails per page: </td>
		<td><input type="text" class="wm_input" onchange="change();" name="intMailsPerPage" size="4" value="<?php echo (int) $settings->MailsPerPage;?>" maxlength="4"></td>
	</tr>
	<tr>
		<td align="right">Default skin: </td>
		<td>
			<select name="txtDefaultSkin" class="wm_input" style="width: 150px;">
			<?php
				$skinsList = &FileSystem::GetSkinsList();
				
				for ($i = 0, $c = count($skinsList); $i < $c; $i++)
				{
					$temp = ($settings->DefaultSkin == $skinsList[$i]) ? 'selected="selected"' : '';
					echo '<option value="'.ConvertUtils::AttributeQuote($skinsList[$i]).'" '. $temp .'> '.$skinsList[$i].'</option>'."\n";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" class="wm_checkbox" onchange="change();" name="intAllowUsersChangeSkin" id="intAllowUsersChangeSkin" value="1" <?php echo ((bool) $settings->AllowUsersChangeSkin) ? 'checked="checked"' : '';?> />
			<label for="intAllowUsersChangeSkin">Allow users to change skin</label>
		</td>
	</tr>
	<tr>
		<td align="right">Default language: </td>
		<td>
			<select name="txtDefaultLanguage" class="wm_input" onchange="change();" style="width: 150px;">
			<?php
				$langList = &FileSystem::GetLangList();

				for ($i = 0, $c = count($langList); $i < $c; $i++)
				{
					$temp = ($settings->DefaultLanguage == $langList[$i]) ? 'selected="selected"' : '';
					echo '<option value="'.ConvertUtils::AttributeQuote($langList[$i]).'" '. $temp .'> '.$langList[$i].'</option>'."\n";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" class="wm_checkbox" name="intAllowUsersChangeLanguage" onchange="change();" id="intAllowUsersChangeLanguage" value="1" <?php echo ((bool) $settings->AllowUsersChangeLanguage) ? 'checked="checked"' : '';?> />
			<label for="intAllowUsersChangeLanguage">Allow users to change interface language</label>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" class="wm_checkbox" name="intShowTextLabels" onchange="change();" id="intShowTextLabels" value="1" <?php echo ((bool) $settings->ShowTextLabels) ? 'checked="checked"' : '';?> />
			<label for="intShowTextLabels">Show text labels</label>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" class="wm_checkbox" name="intAllowAjaxVeersion" onchange="change();" id="intAllowAjaxVeersion" value="1" <?php echo ((bool) $settings->AllowAjax) ? 'checked="checked"' : '';?> />
			<label for="intAllowAjaxVeersion">Allow AJAX Version</label>
		</td>
	</tr>
	<tr>
		<td align="right" >&nbsp;</td>
		<td>
			<input type="checkbox" class="wm_checkbox" name="intAllowDHTMLEditor" onchange="change();" id="intAllowDHTMLEditor" value="1" <?php echo ((bool) $settings->AllowDhtmlEditor) ? 'checked="checked"' : '';?> />
			<label for="intAllowDHTMLEditor">Allow DHTML editor</label>
		</td>
	</tr>
	
	<tr>
		<td align="right" >&nbsp;</td>
		<td>
			<input type="checkbox" class="wm_checkbox" name="intAllowContacts" onchange="change();" id="intAllowContacts" value="1" <?php echo ((bool) $settings->AllowContacts) ? 'checked="checked"' : '';?> />
			<label for="intAllowContacts">Allow Contacts</label>
		</td>
	</tr>
	<tr>
		<td align="right" >&nbsp;</td>
		<td>
			<input type="checkbox" class="wm_checkbox" name="intAllowCalendar" onchange="change();" id="intAllowCalendar" value="1" <?php echo ((bool) $settings->AllowCalendar) ? 'checked="checked"' : '';?> />
			<label for="intAllowCalendar">Allow Calendar</label>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
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
