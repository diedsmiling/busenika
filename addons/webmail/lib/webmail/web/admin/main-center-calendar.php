<!-- [start center] -->

<script language="javascript" type="text/javascript" src="./calendar/inc.calendar-settings.js"></script>
<script type="text/javascript" language="javascript">
<!--
//Calendar Admin Setiings
function ASSettings() {
	
	this.WorkdayStarts	= document.getElementById('WorkdayStarts');
	this.WorkdayEnds	= document.getElementById('WorkdayEnds');
	this.defCountry		= document.getElementById('defCountry');
	this.defTimeZoneCont= document.getElementById('defTimeZoneCont');
	this.timeZoneValue	= "";
	this.allTimeZones	= document.getElementById('allTimeZones');
	this.settings_frm	= document.getElementById('settings_frm');
	this.weekStartsOn	= document.getElementById('weekStartsOn');
	this.defTimeZone    = null;
	this.ServerNameError= false;
	
	var set_workdayStarts		= <?php echo $settings->Cal_WorkdayStarts;?>;
	var set_workdayEnds			= <?php echo $settings->Cal_WorkdayEnds; ?>;
	var set_timeFormat			= <?php echo $settings->Cal_DefaultTimeFormat; ?>;
	var set_allZones			= <?php echo ($settings->Cal_AllTimeZones == 1) ? 1 : 0; ?>;
	this.set_DefaultTimeZone	= <?php echo $settings->Cal_DefaultTimeZone; ?>

	this.SetTimeFormat(this.WorkdayStarts, set_workdayStarts, set_timeFormat);
	this.SetTimeFormat(this.WorkdayEnds,  set_workdayEnds, set_timeFormat);
	this.LoadTimeZones(set_allZones, this.set_DefaultTimeZone);

	//set handlers
	var obj = this;	
	
	/*radiobox changes time format*/
	document.getElementById("defTimeFormat0").onclick = function() {
		obj.SetTimeFormat(obj.WorkdayStarts, obj.WorkdayStarts.value, 1);
	 	obj.SetTimeFormat(obj.WorkdayEnds, obj.WorkdayEnds.value, 1);
	 }
	document.getElementById("defTimeFormat1").onclick = function() {
		obj.SetTimeFormat(obj.WorkdayStarts, obj.WorkdayStarts.value, 2);
	 	obj.SetTimeFormat(obj.WorkdayEnds, obj.WorkdayEnds.value, 2);
	 }	 
	
	/*reload timesones when change country*/	
	this.defCountry.onchange = function() {
		change();
		var allZones = (obj.allTimeZones.checked) ? 1 : 0;
		obj.LoadTimeZones(allZones, ""); 
	}

	this.WorkdayStarts.onblur = function() {
		obj.CheckWorkdayTime();
	}
	this.WorkdayEnds.onblur = function() {
		obj.CheckWorkdayTime();
	}
	
	/*reload timezones when choose all timezones*/
	this.allTimeZones.onclick = function() {
		var allZones = (this.checked) ? 1 : 0;
		obj.LoadTimeZones(allZones, obj.timeZoneValue);
	} 
	
	this.settings_frm.onsubmit = function() {
		return !obj.CheckWorkdayTime();
	}

}
ASSettings.prototype = {

	
	SetTimeFormat: function(WorkdayContainer, WorkdayValue, TimeFormat) {
		
		var k = 0;
		var hour = "";
		var time = "";
		
		for (i=WorkdayContainer.options.length-1; i>=0; i--) {
			WorkdayContainer.options[i] = null;
		}

		for (var i=0; i<24; i++) {
			opt = document.createElement("option");
			if (TimeFormat == 1) {
				if (i==12) k=0;
				if (k==0) hour=12;
				else hour=k;
						
				time = hour + ((i<12) ? " AM" : " PM");
					
				k++;
			} else if (TimeFormat == 2) {
				time = (i<10) ? ("0"+i+":00") : (i+":00");
			}
					
			opt.value = i;
			WorkdayContainer.appendChild(opt);
			opt.text = time;
		}
		setTimeout( function(){WorkdayContainer.options[WorkdayValue].selected=true;}, 1);
	},
	
	CheckWorkdayTime: function() {
		var wrongInterval = false;
		if (parseInt(this.WorkdayStarts.value) >= parseInt(this.WorkdayEnds.value)) {
			wrongInterval = true;
			writeDiv('<font color="red"><b>The "Workday ends" time must be greater than the "Workday starts" time.</b></font>');
		} else {
			wrongInterval = false;
		}
		return wrongInterval;
	},
	
	LoadTimeZones: function(allTimeZones, defaultTimeZone)
	{
        var obj = this;
        var _defTimeZone = "<select id='defTimeZone' style='width: 300px;' name='defTimeZone'>"
        var i = "";
        var code = this.defCountry.value;
        
        if(allTimeZones == 1)
        {
		
            if(this.defTimeZone != null) this.set_DefaultTimeZone = this.defTimeZone.options[this.defTimeZone.selectedIndex].value;

			for(i in timeZoneForCountry[code]) {
				if (timeZoneForCountry[code][i] == this.set_DefaultTimeZone) {
					tmp_zone = this.set_DefaultTimeZone;
					break;
				} else {
					tmp_zone = timeZoneForCountry[code][0];
				}
			}
			this.set_DefaultTimeZone = tmp_zone;
		
		    obj.defTimeZoneCont.innerHTML = allTimeZone;
            this.defTimeZone = document.getElementById("defTimeZone");
			this.defTimeZone.onchange = function() {
				change();
			}
            this.defTimeZone.selectedIndex = parseInt(this.set_DefaultTimeZone, 10) - 1;
        }
        else
        {
            for(i in timeZoneForCountry[code])
            {
                var index = timeZoneForCountry[code][i];
                var timeZoneValue = AllTimeZonesArr[index];
                if(this.set_DefaultTimeZone == index)
                {
                    _defTimeZone += "<option value='" + index + "' selected='selected'>" + timeZoneValue + "</option>\r\n";
                }
                else
                {
                    _defTimeZone += "<option value='" + index + "'>" + timeZoneValue + "</option>\r\n";
                }
            }
            _defTimeZone += "</select>";
            obj.defTimeZoneCont.innerHTML = _defTimeZone;
            this.defTimeZone = document.getElementById("defTimeZone");
            this.set_DefaultTimeZone = this.defTimeZone.options[this.defTimeZone.selectedIndex].value;
        }

	}
}
	//-->
</script>
<form action="?mode=save" method="POST" id="settings_frm">
<input type="hidden" name="form_id" value="calendar">
<table class="wm_admin_center" width="500" border="0">
	<tr>
		<td width="150"></td>
		<td width="160"></td>
	</tr>
	<tr>
		<td colspan="2" class="wm_admin_title">Calendar Settings</td>
	</tr>
	<tr><td colspan="2"><br /></td></tr>
	<tr>
		<td align="right">Default Time Format: </td>
		<td>
			<input type="radio" name="defTimeFormat" id="defTimeFormat0" onchange="change();" value="1" class="wm_checkbox"
			<?php if ($settings->Cal_DefaultTimeFormat == 1) echo 'checked="checked"'; ?> /><label for="defTimeFormat0">1PM</label>
			&nbsp;&nbsp;&nbsp;
			<input type="radio" name="defTimeFormat" id="defTimeFormat1" onchange="change();" value="2" class="wm_checkbox"
			<?php if ($settings->Cal_DefaultTimeFormat == 2) echo 'checked="checked"';?> /><label for="defTimeFormat1">13:00</label>
		</td>
	</tr>

	<tr>
		<td align="right">Default Date Format: </td>
		<td >
		<select name="defDateFormat" id="defDateFormat" onchange="change();">
			<option value="1" <?php if ($settings->Cal_DefaultDateFormat == 1) echo 'selected="selected"';?>><?php echo date("m/d/Y"); ?></option>
			<option value="2" <?php if ($settings->Cal_DefaultDateFormat == 2) echo 'selected="selected"';?>><?php echo date("d/m/Y"); ?></option>			
			<option value="3" <?php if ($settings->Cal_DefaultDateFormat == 3) echo 'selected="selected"';?>><?php echo date("Y-m-d"); ?></option>			
			<option value="4" <?php if ($settings->Cal_DefaultDateFormat == 4) echo 'selected="selected"';?>><?php echo date("M d, Y"); ?></option>
			<option value="5" <?php if ($settings->Cal_DefaultDateFormat == 5) echo 'selected="selected"';?>><?php echo date("d M Y"); ?></option>
		</select>
		</td>
	</tr>
		
	<tr>
		<td></td>
		<td>
			<input type="checkbox" name="showWeekends" id="showWeekends" class="wm_checkbox" onchange="change();" value="1"
			<?php echo ((int) $settings->Cal_ShowWeekends == 1) ? 'checked="checked"' : '';?> />
			<label for="showWeekends">Show weekends</label>
		</td>
	</tr>
	
	<tr>
		<td align="right">Workday starts: </td>
		<td>
			<select style="width:100px" name="WorkdayStarts" id="WorkdayStarts"></select>
			&nbsp;&nbsp;ends:
			<select style="width:100px" name="WorkdayEnds" id="WorkdayEnds"></select>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			 <input type="checkbox" name="showWorkDay" id="showWorkDay" class="wm_checkbox" onchange="change();" value="1" <?php if ($settings->Cal_ShowWorkDay) echo 'checked="checked"';?>/><label for="showWorkDay">Show workday</label>
		</td>
	</tr>
	
	<tr>
		<td align="right">Week starts on: </td>
		<td>
			<select name="weekStartsOn" id="weekStartsOn" onchange="change();">
				<option value="0" <?php if ($settings->Cal_WeekStartsOn == 0) echo 'selected="selected"';?>>Sunday</option>
				<option value="1" <?php if ($settings->Cal_WeekStartsOn == 1) echo 'selected="selected"';?>>Monday</option>			
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Default Tab: </td>
		<td>
			 <input type="radio" name="defTab" class="wm_checkbox" id="defTab0" onchange="change();" value="1" <?php if ($settings->Cal_DefaultTab == 1) echo 'checked="checked"';?>/><label for="defTab0">Day</label>&nbsp;
			 <input type="radio" name="defTab" class="wm_checkbox" id="defTab1" onchange="change();" value="2" <?php if ($settings->Cal_DefaultTab == 2) echo 'checked="checked"';?>/><label for="defTab1">Week</label>&nbsp;
			 <input type="radio" name="defTab" class="wm_checkbox" id="defTab2" onchange="change();" value="3" <?php if ($settings->Cal_DefaultTab == 3) echo 'checked="checked"';?>/><label for="defTab2">Month</label> 			 			 
		</td>
	</tr>
	<tr>
		<td align="right">Default Country: </td>
		<td>
			<select style="width:300px" name="defCountry" id="defCountry"><?php
	
	if (@file_exists(INI_DIR.'/country/country.dat'))
	{
		$countryCode = $countryName = '';
		$i = 1;
		$fp = @fopen(INI_DIR.'/country/country.dat', 'r');
		if ($fp)
		{
			while (!feof($fp))
			{
				$str = trim(fgets($fp));
				list($countryCode, $countryName) = split ('-', $str);
				echo '<option value="'.$countryCode.'"';
				if ($settings->Cal_DefaultCountry == $countryCode)
				{
					echo ' selected="selected"';
				}
				echo '>'.$countryName.'</option>'."\r\n";
				$i++;
				if ($i > 300)
				{
					break;
				}
			}
			@fclose($fp);
		}
	}
	
?></select>	 			 
		</td>
	</tr>
	
	<tr>
		<td align="right">Default Time Zone: </td>
		<td id="defTimeZoneCont">
			<select style="width:280px" name="defTimeZone" id="defTimeZone"></select>
		</td>
	</tr>
	<tr>
		<td></td>	
		<td>
			<input type="checkbox" name="allTimeZones" id="allTimeZones" onchange="change();" value="1" class="wm_checkbox" <?php if ($settings->Cal_AllTimeZones) echo 'checked="checked"';?>/><label for="allTimeZones">All time zones</label>
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
			<input type="submit" name="save" class="wm_button" value="Save" style="width: 100px; font-weight: bold">&nbsp;
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
<!--
var admSet = new ASSettings();
//-->
</script>
<!-- [end center] -->