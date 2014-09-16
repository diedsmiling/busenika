<!-- [start center] -->
<script type="text/javascript">
<!--

	function Run()
	{
		dotest = false;
		mass = Array(
			document.getElementById('txtSqlLogin'),
			document.getElementById('txtSqlPassword'),
			document.getElementById('txtSqlName'),
			document.getElementById('txtSqlDsn'),
			document.getElementById('txtSqlSrc'),
			document.getElementById('odbcConnectionString')
		);
		
		check0 = Array(
			new Array(
				document.getElementById('intDbType0'),
				document.getElementById('labid0')	
			),
			new Array(
				document.getElementById('intDbType1'),
				document.getElementById('labid1')	
			)
		);
		
		check1 = Array(document.getElementById('useCS'));
		buttonCreate = document.getElementById('dodatabase');
	}
	
	function GetCheckeId()
	{
		var tempInt = -1;
		for (var i = 0; i < 2; i++)
		{
			check0[i][1].style.color = (check0[i][0].checked == true) ? "#336699" : "Black";
			if (check0[i][0].checked == true)
			{
				tempInt = i;
			}
		}
		return tempInt;
	}

	function CheckForm()
	{
		var error = false;
		if (check0[0][0].checked == true || check0[1][0].checked == true)
		{
			if (mass[4].value == '')
			{
				error = true;
				mass[4].style.background = '#F39595';
			}
		}
		
		if (check1[0].checked == true && mass[5].value == '')
		{
			error = true;
			mass[5].style.background = '#F39595';
		}
		
		if (error == true)
		{
			writeDiv('<font color="red"><b>You cannot leave this field blank</b></font>');
			return false;
		}
		return true;
	}
	
	function DoIt()
	{
		var tempInt = GetCheckeId();
		for (var w = 0; w < 6; w++)
		{
			mass[w].disabled = false;
			mass[w].style.background = "White";
		}	

		switch (tempInt)
		{
			case 0:
			case 1:
				mass[5].disabled = true;
				mass[5].style.background = "#EEEEEE";
				buttonCreate.disabled = false;
				break;
			case -1:
				check0[0][0].checked = true;
				DoIt();
				break;
		}
		if (check1[0].checked)
		{
			mass[2].disabled = true;
			mass[2].style.background = "#EEEEEE";			
			mass[3].disabled = true;
			mass[3].style.background = "#EEEEEE";			
			mass[4].disabled = true;
			mass[4].style.background = "#EEEEEE";			
			mass[5].disabled = false;
			mass[5].style.background = "White";	
		}
	}
	
	function clearDiv()
	{
		document.getElementById('test_connection').disabled = false;
	}
	
	function formSubmit()
	{
		if (CheckForm())
		{
			var actform = document.getElementById('actionform');
			
			if (dotest)
			{
				dotest = false;
				writeDiv("<font color='Black'><b>processing ...</b></font>");
				
				document.getElementById('test_connection').disabled = true;
				//document.getElementById('messDiv').innerHTML = '<img src="./admin/indicator_arrows.gif">'; 
				actform.action = "mailadm.php?mode=test_connection";
				actform.target = "frm";
			}
			else
			{
				actform.action = "mailadm.php?mode=save";
				actform.target = "_self";
			}
			return true;
		} else return false;
	}
	
	//-->
</script>
<form action="mailadm.php?mode=save" method="POST" name="actionform" id="actionform" onsubmit="return formSubmit();">
<input type="hidden" name="form_id" value="db">
<table class="wm_admin_center" width="500">
	<tr>
		<td width="170"></td>
		<td></td>
	</tr>
	<!-- 1 -->
	<tr>
		<td colspan="2" class="wm_admin_title">Database Settings</td>
	</tr>
	<tr><td colspan="2"><br /></td></tr>
	<tr>
		<td colspan="2" align="left">
			<input type="radio" onchange="change();" style="vertical-align: middle" value="<?php echo DB_MSSQLSERVER;?>" name="intDbType" id="intDbType0" <?php echo isset($checkmass[DB_MSSQLSERVER]) ? $checkmass[DB_MSSQLSERVER] : '';?> onClick="DoIt()">
			<label for="intDbType0" id="labid0"><strong>MS SQL Server</strong></label>	
			<?php echo ($isMsSQLWork) ? '' : '<font color="red">(MS SQL doesn\'t work)</font>'; ?>
		</td>
	</tr>
	<!-- 2 -->
	<tr>
		<td colspan="2" align="left">
			<input type="radio" onchange="change();" value="<?php echo DB_MYSQL;?>" style="vertical-align: middle" name="intDbType" <?php echo isset($checkmass[DB_MYSQL]) ? $checkmass[DB_MYSQL] : '';?> id="intDbType1" onClick="DoIt()">
			<label for="intDbType1" id="labid1"><strong>MySQL</strong></label>
			<?php echo ($isMySQLWork) ? '' : '<font color="red">(MySQL doesn\'t work)</font>'; ?>
		</td>
	</tr>
	<tr>
		<td align="right">SQL login: </td>
		<td>
			<input type="text" class="wm_input" onchange="change();" name="txtSqlLogin" id="txtSqlLogin" value="<?php echo ConvertUtils::AttributeQuote($settings->DbLogin);?>" size="45">
		</td>
	</tr>
	<tr>
		<td align="right">SQL password: </td>
		<td>
			<input type="password" class="wm_input" onchange="change();" name="txtSqlPassword" id="txtSqlPassword" value="<?php echo ConvertUtils::AttributeQuote(trim($settings->DbPassword));?>" size="45">
		</td>
	</tr>
	<tr>
		<td align="right">Database name: </td>
		<td>
			<input type="text" class="wm_input" name="txtSqlName" id="txtSqlName" value="<?php echo ConvertUtils::AttributeQuote($settings->DbName);?>" size="45">
		</td>
	</tr>		
	<tr>
		<td align="right">Data source (DSN): </td>
		<td>
			<input type="text" class="wm_input" onchange="change();" name="txtSqlDsn" id="txtSqlDsn" value="<?php echo ConvertUtils::AttributeQuote($settings->DbDsn);?>" size="45">
		</td>
	</tr>		
	<tr>
		<td align="right">Host: </td>
		<td>
			<input type="text" class="wm_input" onkeyup="RedThis(this);" name="txtSqlSrc" id="txtSqlSrc" value="<?php echo ConvertUtils::AttributeQuote($settings->DbHost);?>" size="45">
		</td>
	</tr>	
	<!-- 4 -->
	<tr>
		<td colspan="2">
		</td>
	</tr>
	<tr>
		<td align="right"><br /><strong>ODBC Connection String:</strong>
		<?php echo ($isOdbcWork) ? '' : '<br /><font color="red">(ODBC dosn\'t work)</font> '; ?>
		</td>
		<td align="left"><br />
		<input type="text" class="wm_input" onkeyup="RedThis(this);" name="odbcConnectionString" id="odbcConnectionString" value="<?php echo ConvertUtils::AttributeQuote($settings->DbCustomConnectionString);?>" size="45">
		</td>
	</tr>
		<tr>
		<td align="right"></td>
		<td>
			<input type="checkbox" value="1" onchange="change();" class="wm_checkbox" name="useCS" id="useCS" <?php echo ($settings->UseCustomConnectionString) ? 'checked="checked"' : '';?> onclick="DoIt();" />
			<label for="useCS">Use connection string</label>	
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
		<td align="left">
			<input type="submit" name="test_connection" id="test_connection" value="Test Connection" onclick="dotest = true;" style="width: 150px; float: left; font-weight: bold" /><br />
			<input type="button" name="dodatabase" id="dodatabase" onclick="CreateTable();"
				value="Create Tables" style="width: 150px; float: left; font-weight: bold" />
		</td>
		<td align="right">
			<input type="submit" name="submit" value="Save" class="wm_button" style="width: 100px;">&nbsp;
		</td>
	</tr>
</table>
</form>
<iframe name="frm" height="0" width="0" style="visibility: hidden;"></iframe>
<script type="text/javascript">
<!--	
	Run();
	DoIt();
//-->
</script>
<!-- [end center] -->