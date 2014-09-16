/*
Classes:
	CNewAccountForm
*/

function CNewAccountForm()		
	{
		this.fm_protocol			= document.getElementById("fm_protocol");
		this.fm_advanced_options	= document.getElementById("pop_advanced");
		this.form					= document.getElementById("accform");
		
		this.fm_inbox_sync 				= document.getElementById("fm_inbox_sync");
		this.fm_mail_management_mode1 	= document.getElementById("fm_mail_management_mode1");
		this.fm_mail_management_mode2 	= document.getElementById("fm_mail_management_mode2");
		this.fm_keep_for_x_days			= document.getElementById("fm_keep_for_x_days");
		this.fm_keep_messages_days 		= document.getElementById("fm_keep_messages_days");
		this.fm_delete_messages_from_trash = document.getElementById("fm_delete_messages_from_trash");
		this.fm_int_deleted_as_server	= document.getElementById("fm_int_deleted_as_server");

		this.pop_advanced	= document.getElementById("pop_advanced");
		this.arr_inputs		= this.pop_advanced.getElementsByTagName("input");
		
		this.email = document.getElementById("fm_email");
		this.inc_server = document.getElementById("fm_inc_server");
		this.incoming_port = document.getElementById("fm_inc_server_port");
		this.smtp_server_port = document.getElementById("fm_smtp_server_port");
		this.inc_password = document.getElementById("fm_inc_password");
		this.inc_login = document.getElementById("fm_inc_login");
		this.submitbtn = document.getElementById("subm1")
	}
	
CNewAccountForm.prototype = {
		ShowPOP3AdvancedOptions: function ()
		{

			this.SetDefaultData();
			var obj = this;
			obj.fm_protocol.onchange = function() {
				if (obj.fm_protocol.value == "imap") {
					obj.fm_advanced_options.className = "wm_hide";
					obj.incoming_port.value = "143";
				} else {
					obj.fm_advanced_options.className = "";
					obj.incoming_port.value = "110";
				}
			}
			
			obj.fm_inbox_sync.onchange = function() {
				if (obj.fm_inbox_sync.value == 1) {
					obj.fm_mail_management_mode1.disabled	= true;
					obj.fm_mail_management_mode1.checked	= false;
					obj.fm_mail_management_mode2.disabled	= false;
					obj.fm_mail_management_mode2.checked	= true;
					obj.fm_keep_for_x_days.disabled		= false;
					obj.fm_delete_messages_from_trash.disabled = false;
					obj.fm_int_deleted_as_server.disabled	= false;
					if (obj.fm_keep_for_x_days.checked) {
						obj.fm_keep_messages_days.disabled	= false;
					} else {
						obj.fm_keep_messages_days.disabled	= true;
					}
				} else if (obj.fm_inbox_sync.value == 3) {
					obj.fm_mail_management_mode1.disabled	= false;
					obj.fm_mail_management_mode2.disabled	= false;
					obj.fm_keep_for_x_days.disabled			= false;
					obj.fm_delete_messages_from_trash.disabled = false;
					obj.fm_int_deleted_as_server.disabled	= false;
					if (obj.fm_keep_for_x_days.checked)
					{
						obj.fm_keep_messages_days.disabled	= false;
					} else {
						obj.fm_keep_messages_days.disabled	= true;
					}
				} else if (obj.fm_inbox_sync.value == 5) {
					for (i=0; i<obj.arr_inputs.length; i++)
					{
						obj.arr_inputs[i].disabled = true;
					}
				}
			}//fm_inbox_sync.onchange
			
			obj.fm_keep_for_x_days.onclick = function() {
				if (obj.fm_keep_for_x_days.checked == true) {
					obj.fm_keep_messages_days.disabled = false;
				} else {
					obj.fm_keep_messages_days.disabled = true;
				}
			}

			obj.fm_mail_management_mode1.onclick = function() {
				obj.fm_mail_management_mode1.disabled	= false;
				obj.fm_mail_management_mode2.disabled	= false;
				obj.fm_keep_for_x_days.disabled		= true;
				obj.fm_delete_messages_from_trash.disabled = true;				
				obj.fm_int_deleted_as_server.disabled	= false;		
				obj.fm_keep_messages_days.disabled = true;
			}
			obj.fm_mail_management_mode2.onclick = function() {
				obj.fm_mail_management_mode1.disabled	= false;
				obj.fm_mail_management_mode2.disabled	= false;
				obj.fm_keep_for_x_days.disabled			= false;
				obj.fm_delete_messages_from_trash.disabled = false;				
				obj.fm_int_deleted_as_server.disabled	= false;
				if (obj.fm_keep_for_x_days.checked) {
					obj.fm_keep_messages_days.disabled = false;
				} else {
					obj.fm_keep_messages_days.disabled = true;
				}
			}
			
		},
		
		SetDefaultData: function()
		{
			if (this.fm_protocol.value == "imap")
			{
				this.fm_advanced_options.className = "wm_hide";
				//		this.incoming_port.value = "143";
		
			} else {
				this.fm_advanced_options.className = "";
				//		this.incoming_port.value = "110";
			}
			
			if (this.fm_inbox_sync.value == 1) {
				this.fm_mail_management_mode1.disabled	= true;
				this.fm_mail_management_mode2.disabled	= false;
				this.fm_keep_for_x_days.disabled			= false;
				this.fm_delete_messages_from_trash.disabled = false;
				this.fm_int_deleted_as_server.disabled	= false;
				if (this.fm_keep_for_x_days.checked)
				{
					this.fm_keep_messages_days.disabled	= false;
				} else {
					this.fm_keep_messages_days.disabled	= true;
				}
			}

			if (this.fm_inbox_sync.value == 3) {
				if (this.fm_mail_management_mode1.checked == true)
				{
					this.fm_mail_management_mode2.disabled = false;
					this.fm_keep_for_x_days.disabled	= true;
					this.fm_keep_messages_days.disabled	= true;
					this.fm_delete_messages_from_trash.disabled = true;
				}
				if (this.fm_mail_management_mode2.checked == true)
				{
					this.fm_mail_management_mode1.disabled = false;				
					this.fm_keep_for_x_days.disabled	= false;
					this.fm_delete_messages_from_trash.disabled = false;
					if (this.fm_keep_for_x_days.checked)
					{
						this.fm_keep_messages_days.disabled	= false;
					} else {
						this.fm_keep_messages_days.disabled	= true;
					}
				}
				this.fm_int_deleted_as_server.disabled	= false;
			}
			
			if (this.fm_inbox_sync.value == 5)
			{
				for (i=0; i<this.arr_inputs.length; i++)
				{
					this.arr_inputs[i].disabled = true;
				}
			}
		},//setDefaultData
		
		CheckFields: function()
		{
			var obj = this;
			var re = /^[0-9]{1,5}$/;
			var re1 = /[0-9a-z_]+@[0-9a-z_^.]+.[a-z]{2,3}/i
			 
			this.form.onsubmit = function() {
				if (!DoAlert()) return false;
				if (obj.email.value.length == 0) { alert(Lang.WarningEmailFieldBlank);  return false;}
				else if (!re1.test(obj.email.value)) { alert(Lang.WarningCorrectEmail); return false;}
				else if (obj.inc_server.value.length == 0) {alert(Lang.WarningIncServerBlank); return false;}
				else if (obj.incoming_port.value.length == 0) {alert(Lang.WarningIncPortBlank); return false;}
				else if (!re.test(obj.incoming_port.value)) { alert(Lang.WarningIncPortNumber); return false;}
				else if (obj.inc_login.value.length == 0) {alert(Lang.WarningIncLoginBlank); return false;}
				else if (obj.smtp_server_port.value.length == 0) { alert(Lang.WarningOutPortBlank); return false;}
				else if (!re.test(obj.smtp_server_port.value)) { alert(Lang.WarningOutPortNumber); return false;}
				else if (obj.inc_password.value.length == 0) {alert(Lang.WarningIncPassBlank); return false;}
				else if (obj.fm_mail_management_mode2.checked && !re.test(obj.fm_keep_messages_days.value)) {
					alert(Lang.WarningMailsOnServerDays);
					return false;
				}
				else {return true;}
			}
		}
}