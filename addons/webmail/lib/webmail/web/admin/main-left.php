<?php

$navArray = array('','','','','','','','', '');
$navArray[$navId] = ' class="wm_selected_settings_item"';

?>
	<table class="wm_settings">
		<tr>
			<!-- [start navigation] -->
			<td class="wm_settings_nav">
				<ul>
				<div<?php echo $navArray[1];?>>
					<nobr><li><a href="?mode=wm_db" onclick="return SaveForm();">Database Settings</a></li></nobr>
				</div>
				<div<?php echo $navArray[2];?>>
					<nobr><li><a href="?mode=wm_users&clear=1" onclick="return SaveForm();">Users Management</a></li></nobr>
				</div>
				<div<?php echo $navArray[3];?>>
					<nobr><li><a href="?mode=wm_settings" onclick="return SaveForm();">WebMail Settings</a></li></nobr>
				</div>
				<div<?php echo $navArray[4];?>>
					<nobr><li><a href="?mode=wm_interface" onclick="return SaveForm();">Interface Settings</a></li></nobr>
				</div>
				<div<?php echo $navArray[5];?>>
					<nobr><li><a href="?mode=wm_domain" onclick="return SaveForm();">Login Settings</a></li></nobr>
				</div>
				<div<?php echo $navArray[8];?>>
					<nobr><li><a href="?mode=wm_cal" onclick="return SaveForm();">Calendar Settings</a></li></nobr>
				</div>	
				<div<?php echo $navArray[6];?>>
					<nobr><li><a href="?mode=wm_debug" onclick="return SaveForm();">Debug Settings</a></li></nobr>
				</div>	
				<div<?php echo $navArray[7];?>>
					<nobr><li><a href="?mode=wm_server" onclick="return SaveForm();">Mail Server Integration</a></li></nobr>
				</div>		
				</ul>		
			</td>
			<!-- [end navigation] -->
			<td class="wm_settings_cont" valign="top">