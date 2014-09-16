<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'class_mailprocessor.php');
	require_once(WM_ROOTPATH.'class_contacts.php');
	
	@session_name('PHPWEBMAILSESSID');
	@session_start();

	if (!isset($_SESSION[ACCOUNT_ID])) exit();
	
	$settings = Settings::CreateInstance();
	if (!$settings || !$settings->isLoad || !$settings->IncludeLang()) exit();	
	if (!$settings->AllowContacts) exit();	
	
	$account =& Account::LoadFromDb($_SESSION[ACCOUNT_ID]);
	if (!$account) exit();	
	
	define('defaultSkin', $account->DefaultSkin);
	$emails = array();
	$htmlbuff = '';
	$f = isset($_GET['f']) ? (int) $_GET['f'] : 1;
	if ($f > 3) $f = 1;
	
	$dbStorage =& DbStorageCreator::CreateDatabaseStorage($account);
	if ($dbStorage->Connect())
	{
		$contacts =& $dbStorage->LoadContactsAndGroups(1, 3, 1);
	}
	
	if ($contacts && $contacts->Count() > 0)
	{
		for ($i = 0, $c = $contacts->Count(); $i < $c; $i++)
		{
			$contact =& $contacts->Get($i);
			$temp = '';
			if ($contact)
			{
				if ($contact->IsGroup)
				{
					$emailsOfGroup = '';
					$groupContacts =& $dbStorage->SelectAddressGroupContacts($contact->Id);		
					
					for ($j = 0, $t = $groupContacts->Count(); $j < $t; $j++)
					{
						$contactOfGroup = $groupContacts->Get($j);
						if (strlen($contactOfGroup->Email) > 0)
						{
							$emailsOfGroup .= (strlen($contactOfGroup->Name) > 0) 
								? ($contactOfGroup->UseFriendlyName) 
									? '&quot;'.$contactOfGroup->Name.'&quot; <'.$contactOfGroup->Email . '>, '
									: $contactOfGroup->Email . ', '
								: $contactOfGroup->Email . ', ';
						}
					}
					$emailsOfGroup = trim(trim($emailsOfGroup), ',');
					$htmlbuff .= '
	<tr class="wm_inbox_read_item" id="g_'.$contact->Id.'">
		<td style="width: 22px; text-align: center;" id="none">
			<input type="checkbox" />
		</td>
		<td class="wm_inbox_from_subject">
			<img src="skins/'.defaultSkin.'/contacts/group.gif"> '.str_replace('<', '&lt;', str_replace('>', '&gt;', $contact->Name)).'
			<input type="hidden" value="'.$emailsOfGroup.'" />
		</td>
	</tr>';
				}
				else 
				{
					$temp = ($contact->UseFriendlyName && strlen(trim($contact->Name)) > 0) ? '&quot;'.$contact->Name.'&quot; ' : '';
					$temp .= ($contact->UseFriendlyName && strlen(trim($contact->Name)) > 0) ? '<'.$contact->Email.'>' : $contact->Email;
					$htmlbuff .= '
	<tr class="wm_inbox_read_item" id="c_'.$contact->Id.'">
		<td style="width: 22px; text-align: center;" id="none">
			<input type="checkbox" />
		</td>
		<td class="wm_inbox_from_subject">
			'.str_replace('<', '&lt;', str_replace('>', '&gt;', $temp)).'
			<input type="hidden" value="'.$temp.'" />
		</td>
	</tr>';
				}
			}
			
			
		}
	}
	else 
	{
		$htmlbuff .= '
		<tr class="wm_inbox_read_item" id="c_001">
			<td style="width: 22px; text-align: center;" id="none">
				<input type="checkbox" class="wm_hide"/>
			</td>
			<td class="wm_inbox_from_subject">
				No contact
				<input type="hidden" value="" />
			</td>
		</tr>';		
	}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title>WebMail Pro</title>
	<script language="JavaScript" type="text/javascript" src="class.common.js"></script>
	<script language="JavaScript" type="text/javascript" src="./classic/base.contactsmini.js"></script>
	<link rel="stylesheet" href="skins/<?php echo defaultSkin;?>/styles.css" type="text/css" id="skin" />
	<script>
function WriteEmails(str)
{
	if (window.opener && window.opener.WriteEmails) {
		window.opener.WriteEmails(str, <?php echo $f; ?>);
	}
	window.close();
}
	</script>
</head>
<body>
<div class="wm_inbox_lines" style="width: auto;">
<table id="list" style="width: 100%;">
	<?php echo $htmlbuff; ?>
	<tr>
		<td colspan="2" align="center">
			<input type="button" value="<?php echo JS_LANG_OK; ?>" onclick="WriteEmails(clist.getContactsAsString())"/>
		</td>
	</tr>
</table>
</div>
<script>
var clist, Browser;
function init()
{
	Browser = new CBrowser();
	clist = new CContactsSelectionMini();
	clist.FillContacts();
	
	var outH = clist.list.offsetHeight + window.outerHeight - window.innerHeight;
	if (window.outerHeight == null || window.innerHeight == null) {
		outH = 400;
	}
	
	window.resizeTo(300, outH);
}
init();
</script>
</body>
</html>