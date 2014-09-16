<?php

	header('Content-Type: text/html; charset=utf-8');

	@session_name('PHPWEBMAILSESSID');
	@session_start();

	function fixed_array_map_stripslashes($array)
	{
		if (is_array($array))
		{
			foreach ($array as $key => $value)
			{
				$array[$key] = (is_array($value))
						? @fixed_array_map_stripslashes($value)
						: @stripslashes($value);
			}
		}
		return $array;
	}
	
	function disable_magic_quotes_gpc()
	{
		if (@get_magic_quotes_gpc() == 1)
		{
			$_REQUEST = fixed_array_map_stripslashes($_REQUEST);
			$_POST = fixed_array_map_stripslashes($_POST);
		}
	}
	
	@disable_magic_quotes_gpc();
	
	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/'));

	require_once(WM_ROOTPATH.'common/inc_constants.php');
	$lang = isset($_SESSION[SESSION_LANG]) ? $_SESSION[SESSION_LANG] : '';
	if ($lang && file_exists(WM_ROOTPATH.'lang/'.$lang.'.php'))
	{
		require_once(WM_ROOTPATH.'lang/'.$lang.'.php');
	}
	
	require_once(WM_ROOTPATH.'common/class_logger.php');
	require_once(WM_ROOTPATH.'class_account.php');
	require_once(WM_ROOTPATH.'classic/base_defines.php');
	
	$log = &new Logger();
	
	if (!Session::has(ACCOUNT_ID))
	{
		echo '<script>parent.changeLocation("'.LOGINFILE.'?error=1");</script>';
		exit();
	}	
	
	$_SESSION['attachtempdir'] = Session::val('attachtempdir', md5(session_id()));
	$account = &Account::LoadFromDb(Session::val(ACCOUNT_ID), -1);
	
	if (!$account)
	{
		echo '<script>parent.changeLocation("'.LOGINFILE.'?error=2");</script>';
		exit();
	}
	
	$isNull = false;
	$isError = false;
	
	switch (Get::val('mode', 'none'))
	{
		case 'preview':
			$mes_id = Post::val('m_id');
			$mes_uid = Post::val('m_uid');
			$folder_id = Post::val('f_id');
			$folder_name = Post::val('f_name'); $folder_name = 'defaultname';
			$mes_charset = Post::val('charset', -1);
			
if (isset($_POST['m_id']))
{
	
	require_once(WM_ROOTPATH.'classic/class_getmessagebase.php');
	
	$error = '';
	$message = &new GetMessageBase(	$account,
									$mes_id,
									$mes_uid,
									$folder_id ,
									$folder_name,
									$mes_charset);
									
	if (!$message->msg) 
	{
		$isNull = true;
		$isError = true;
		break;
	}
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>iframe</title>
	<link rel="stylesheet" href="./skins/<?php echo $message->account->DefaultSkin;?>/styles.css" type="text/css" />
	<script language="JavaScript" type="text/javascript" src="classic/_classes_message.js"></script>
	<script language="JavaScript" type="text/javascript" src="_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="_language.js.php"></script>
	<script language="JavaScript">
	
	parent.InfoPanel.Hide();
	
	function PrevImg(href)
	{
		var shown = window.open(href, 'Popup', 'toolbar=yes,status=no,scrollbars=yes,resizable=yes,width=760,height=480');
		shown.focus();
	}
	
	function SeeImages()
	{
		var temp = document.getElementById('message');
		var imgs = document.getElementsByTagName('img');
		for (var i=0; i<imgs.length; i++)
		{
			if (imgs[i].id)
			{
				imgs[i].src = imgs[i].id;
			}
		}
		document.getElementById('displayimg').className = 'wm_hide';
	}
	
	function ResizeElements(mode)
	{		
		if (mode == 'all')
		{
			document.body.scroll = 'no';
			document.body.style.overflow = 'hidden';
		
			var width = GetWidth();
			var height = GetHeight();
		<?php
		if ($message->msg->Attachments != null && $message->msg->Attachments->Count() > 0)
		{
			echo '

			Headers.width = width;

			Message.width = width - Attachments.width - VResizer.width - 18;
			VResizer.width = 1;
			
			Attachments.height = height - Headers.height;
			Message.height = Attachments.height - 16;
			VResizer.height = Attachments.height;

			Headers.updateSize();
			Message.updateSize();
			
			Attachments.updateSize();
			VResizer.updateSize();
		}
		
		if (mode == \'height\')
		{
			var width = GetWidth();
			Attachments.width = VResizer.x;
			Message.width = width - Attachments.width - VResizer.width - 18;
			Attachments.updateSize();
			Message.updateSize();
		}
			';
		}
		else 
		{
			echo '
			Headers.width = width;
			Message.width = width - 16;
			
			//Headers.height = 60;
			Message.height = height - Headers.height - 16;
	
			Headers.updateSize();
			Message.updateSize();
		}
			';
		}
		
		?>
	}
	
	function DoPost()
	{
		parent.ChangeCharset(document.getElementById('strCharset').value);
		parent.BaseForm.Form.submit();
		return false;
	}

	function ChangeBody(type)
	{
		parent.BaseForm.Plain.value = type;
		parent.BaseForm.Form.submit();
		return false;
	}
	
	function ChangeList()
	{
		var idline = '<?php echo $message->messId?>' + parent.sep + '<?php echo $message->messUid?>' + 
				parent.sep + '<?php echo $message->folderId?>' + parent.sep + '<?php echo $message->charset?>' + parent.sep;
		var subj = "<?php echo str_replace('"', '\\"', ConvertUtils::WMHtmlSpecialChars($message->msg->GetSubject(true))); ?>";
		var from = "<?php echo str_replace('"', '\\"', ConvertUtils::WMHtmlSpecialChars($message->msg->GetFromAsStringForSend()));?>";
				
		parent.InboxLines.UpdateSubject(idline, subj, from);
		parent.InboxLines.SetParams([idline], 'Read', true, false);
	}
	
	</script>
	</head>
	<body onresize="ResizeElements('all');" style="background: #E9F2F8;" scroll="no" style="overflow: hidden;">
	<table class="wm_mail_container" id="wm_mail_container" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3" id="td_message_headers">
			
								<div  class="wm_message_headers" id="message_headers">
									<div>
										<span id="message_from" class="wm_message_left wm_message_resized"><font><?php echo JS_LANG_From;?>:</font>
										<?php 
											echo ConvertUtils::WMHtmlSpecialChars($message->PrintFrom(true));
										?>
										</span>
										<?php
										$isHtml = $message->msg->HasHtmlText();
										if ($message->GetTypeOfMessage() > 2)
										{
											if ($_POST['plain'] == -1 || $_POST['plain'] == 3)
											{
												echo '<span id="message_switcher" class="wm_message_right"><a href="#" onclick="ChangeBody(2); return false;">Switch to Plain Text View</a>&nbsp;&nbsp;</span>';
												$isHtml = true;
											}
											else 
											{
												echo '<span id="message_switcher" class="wm_message_right"><a href="#" onclick="ChangeBody(3); return false;">Switch to Html View</a>&nbsp;&nbsp;</span>';
												$isHtml = false;
											}
										}
										?>
									</div>
									<div>
										<span id="message_to" class="wm_message_left wm_message_resized"><font><?php echo JS_LANG_To;?>:</font>
										<?php 
											echo ConvertUtils::WMHtmlSpecialChars($message->PrintTo(true));
										?>
										</span>
										<span id="message_date" class="wm_message_left"><font><?php echo JS_LANG_Date;?>:</font>
										<?php 
											echo ConvertUtils::WMHtmlSpecialChars($message->PrintDate());
										?>
										</span>
									</div>
									<?php
									$cc = $message->PrintCc(true);
									if ($cc && strlen($cc) > 0)
									{
										echo '
									<div>
										<span id="message_cc" class="wm_message_left wm_message_resized"><font>'.JS_LANG_CC.':</font>
										'.
											ConvertUtils::WMHtmlSpecialChars($cc)
										.'
										</span>
									</div>';
									}
									$bcc = $message->PrintBcc(true);
									if ($bcc && strlen($bcc) > 0)
									{
										echo '
									<div>
										<span id="message_cc" class="wm_message_left wm_message_resized"><font>'.JS_LANG_BCC.':</font>
										'.
											ConvertUtils::WMHtmlSpecialChars($bcc)
										.'
										</span>
									</div>';
									}
									?>
									<div>
										<span id ="message_subject" class="wm_message_left wm_message_resized"><font><?php echo JS_LANG_Subject;?>:</font>
										<?php
											echo ConvertUtils::WMHtmlSpecialChars($message->PrintSubject(true));
											$isHideCharset = ($message->msg->HasCharset) ? 'class="wm_hide"' : 'class="wm_message_right"';
											if (Post::val('charset') != '-1') $isHideCharset = 'class="wm_message_right"';

										?>
										</span>
										<span id ="message_charset" <?php echo $isHideCharset ?>>
											<font><?php echo JS_LANG_Charset;?>:</font>
											<select name="str_charset" id="strCharset" onchange="DoPost();">
											<?php
											
												foreach ($CHARSETS as $value)
												{
													if (Post::val('charset', '-1') == $value[0])
													{
														echo '<option value="'.$value[0].'" selected="selected" > '.$value[1].'</option>'."\r\n";
													}
													else 
													{
														echo '<option value="'.$value[0].'" > '.$value[1].'</option>'."\r\n";
													}
												}
											?>
											</select>
										</span>
									</div>
								</div>
			</td>
		</tr>
		<tr>
			<td id="td_attachments">
		<?php	
		
		$JSfilenameTrim = '';
		if ($message->msg->Attachments != null && $message->msg->Attachments->Count() > 0)
		{
			echo '<div id="attachments" class="wm_message_attachments">';
			
			$attachments = &$message->msg->Attachments;
			if ($attachments != null && $attachments->Count() > 0)
			{
				foreach (array_keys($attachments->Instance()) as $key)
				{
					$attachment = &$attachments->Get($key);
					$tempname = $message->msg->IdMsg.'-'.$key.'_'.$attachment->GetTempName();
					//$filename = ConvertUtils::ConvertEncoding($attachment->GetFilenameFromMime(), $GLOBALS[MailInputCharset], $message->account->GetUserCharset());
					$filename = $attachment->GetFilenameFromMime();
					$filesize = GetFriendlySize(strlen($attachment->MimePart->GetBinaryBody()));
										
					$fs = &new FileSystem(INI_DIR.'/temp', $message->account->Email, $message->account->Id);
					$attfolder = &new Folder($message->account->Id, -1, $_SESSION['attachtempdir']);
					if (!$fs->SaveAttach($attachment, $attfolder, $tempname))
					{
						$log->WriteLine('Save temp Attachment error: '.$GLOBALS[ErrorDesc]);
					}
							
					$ContentType = ConvertUtils::GetContentTypeFromFileName($filename);
					
					$JSfilenameTrim .= '
					att = document.getElementById("at_'.$key.'");
					if (att.innerHTML.length > 16) 
							att.innerHTML = att.innerHTML.substring(0, 15) + \'&#8230;\';
					';
					
					echo '
					<div style="float: left;"><a href="attach.php?tn='.$tempname.'&filename='.urlencode($filename).'">
							<img src="./images/icons/'.GetAttachImg($filename).'" title="Click to download '.$filename.' ('.$filesize.')" /></a><br />
							<span id="at_'.$key.'" title="Click to download '.$filename.' ('.$filesize.')">'.$filename.'</span><br />';

					if (strpos($ContentType, 'image') !== false)
					{
						echo '<a href="#" onclick="PrevImg(\'view_image.php?tn='.$tempname.'\')">View</a>';
					}
					
					echo '</div>';
				}
				
			}
									
			echo '</div>
			</td>
			<td rowspan="3" id="td_vert_resizer"><div id="vert_resizer" class="wm_vresizer_mess"></div></td>';
		}
		else 
		{
			echo '</td><td></td>';
		}
		?>
			
			<td id="td_message">
			<div id="message" class="wm_message">
			<?php
				echo ($isHtml) ?	
				//'<div><span id="displayimg"><b>Images are not displayed.</b> <a href="#" onclick="SeeImages()" >Display images below?</a><br /><br /></span></div>'.
				ConvertUtils::ReplaceJSMethod($message->PrintHtmlBody(true)) : $message->PrintPlainBody();
			?>
			</div>
			</td>		
		</tr>
	</table>
	<script language="JavaScript">	
	function Init()
	{
		Headers = new CHeaders();
		Message = new CMessage();
		
		<?php
		if ($message->msg->Attachments != null && $message->msg->Attachments->Count() > 0)
		{
			echo '
			Attachments = new CAttachments();
			VResizer = new CVResizer();';
		}
		?>
		
		ResizeElements('all');
		if (Headers.to && Headers.date && (Headers.to.offsetWidth + Headers.date.offsetWidth) > Headers.width)
		{
			Headers.to.style.width = (Headers.width - Headers.date.offsetWidth - 50) + 'px';
		}
		
		ChangeList();
	}
	Init();
	<?php
	echo $JSfilenameTrim;
	?>
	</script>
	
	
	</body>
	</html>	
	
<?php
} else $isNull = true;
			break;
			
		case 'full':
			$mes_id = Post::val('m_id');
			$mes_uid = Post::val('m_uid');
			$folder_id = Post::val('f_id');
			$folder_name = Post::val('f_name', ''); 
			$folder_name = ($folder_name) ? $folder_name : 'defaultname';
			$mes_charset = Post::val('charset', -1);
			
			
if (isset($_POST['m_id']))
{
	
	require_once(WM_ROOTPATH.'classic/class_getmessagebase.php');
	
	$error = '';
	$message = &new GetMessageBase(	$account,
									$mes_id,
									$mes_uid,
									$folder_id ,
									$folder_name,
									$mes_charset);
			
									
	if (!$message->msg) 
	{
		$isNull = true;
		$isError = true;
		break;
	}
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>iframe</title>
	<link rel="stylesheet" href="./skins/<?php echo $message->account->DefaultSkin;?>/styles.css" type="text/css" />
	<script language="JavaScript" type="text/javascript" src="_functions.js"></script>
	<script language="JavaScript" type="text/javascript" src="classic/_classes_message.js"></script>
	<script language="JavaScript" type="text/javascript" src="_language.js.php"></script>
	<script language="JavaScript">
	
	parent.InfoPanel.Hide();
	
	function PrevImg(href)
	{
		var shown = window.open(href, 'Popup', 'toolbar=yes,status=no,scrollbars=yes,resizable=yes,width=760,height=480');
		shown.focus();
	}
	
	function SeeImages()
	{
		var temp = document.getElementById('message');
		var imgs = document.getElementsByTagName('img');
		for (var i=0; i<imgs.length; i++)
		{
			if (imgs[i].id)
			{
				imgs[i].src = imgs[i].id;
			}
		}
		document.getElementById('displayimg').className = 'wm_hide';
	}
	
	function ResizeElements(mode)
	{		
		document.body.scroll = 'no';
		document.body.style.overflow = 'hidden';
		
		var width = GetWidth();
		var height = GetHeight();
			
		if (mode == 'all')
		{

		<?php
		
		if ($message->msg->Attachments != null && $message->msg->Attachments->Count() > 0)
		{
			$temp = ($message->GetTypeOfMessage() > 2) ? 'Attachments.height = Attachments.height - document.getElementById("lowtoolbar").offsetHeight' : '';
			echo '

			Headers.width = width;
			VResizer.width = 1;
			Message.width = width - Attachments.width - VResizer.width - 16 ;
			
			Attachments.height = height - Headers.height;
			'.$temp.'
			Message.height = Attachments.height - 16;
			VResizer.height = Attachments.height;
			

			Headers.updateSize();
			Message.updateSize();
			
			Attachments.updateSize();
			VResizer.updateSize();
		}
		
		if (mode == \'height\')
		{
			var width = GetWidth();
			Attachments.width = VResizer.x;
			Message.width = width - Attachments.width - VResizer.width - 16;
			Attachments.updateSize();
			Message.updateSize();
		}
			';
		}
		else 
		{
			$temp = ($message->GetTypeOfMessage() > 2) ? 'Message.height = Message.height - document.getElementById("lowtoolbar").offsetHeight' : '';
			echo '
			Headers.width = width;
			Message.width = width - 16;
			
			//Headers.height = 60;
			Message.height = height - Headers.height - 16;
			'.$temp.'
			
			Headers.updateSize();
			Message.updateSize();
		}
			';
		}
		
		?>
	}
	
	function DoPost()
	{
		parent.ChangeCharset(document.getElementById('strCharset').value);
		parent.BaseForm.Form.submit();
		return false;
	}

	function ChangeBody(type)
	{
		parent.BaseForm.Plain.value = type;
		parent.BaseForm.Form.submit();
		return false;
	}	
	
	</script>
	</head>
	<body onresize="ResizeElements('all');" style="background: #E9F2F8;" scroll="no" style="overflow: hidden;">
	<div class="wm_hide" id="headersCont">
		<div id="headersDiv" class="wm_message_rfc822"><pre><?php
		echo ConvertUtils::WMHtmlSpecialChars(
				$message->msg->ClearForSend(
					ConvertUtils::ConvertEncoding(
						$message->msg->OriginalHeaders, $GLOBALS[MailInputCharset], $account->GetUserCharset())));
		?></pre>
		</div>
		<div class="wm_hide_headers"><a href="#" onclick="return FullHeaders.Hide();">Close</a></div>
	</div>
	<table class="wm_mail_container" id="wm_mail_container" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3" id="td_message_headers">
				<table class="wm_view_message" id="message_headers">
					<tr>
						<td class="wm_view_message_title">From:</td>
						<td>
							<span id="fromSpan"><?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintFrom(true)); ?></span>
							<?php
							if (!$message->account->HideContacts)
							{ ?>
							<img class="wm_add_address_book_img" onclick="parent.AddContact(document.getElementById('fromSpan').innerHTML);" title="Add to Address Book" src="skins/<?php echo $account->DefaultSkin; ?>/contacts/save.gif"/>
							<?php } ?>
						</td>
						<td class="wm_headers_switcher">
							<nobr><a href="#" id="fullheadersControl" onclick="return FullHeaders.Show();">Show Full Headers</a></nobr>
						</td>
					</tr>
					<tr>
						<td class="wm_view_message_title">To:</td>
						<td colspan="2"><?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintTo(true)); ?></td>
					</tr>
					<tr>
						<td class="wm_view_message_title">Date:</td>
						<td colspan="2"><?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintDate());	?></td>
					</tr>
					<tr>
						<td class="wm_view_message_title">Subject:</td>
						<td><?php echo ConvertUtils::WMHtmlSpecialChars($message->PrintSubject(true)); 
								
							$isHtml = $message->msg->HasHtmlText();
							if ($message->GetTypeOfMessage() > 2)
							{
								$isHtml = (Post::val('plain', '-1') == -1 || Post::val('plain', '-1') == 3);
							}
											
							$isHideCharset = ($message->msg->HasCharset) ? ' class="wm_hide"' : '';
							if (Post::val('charset') != '-1') $isHideCharset = '';
						?></td>
					</tr>
					<tr<?php echo $isHideCharset; ?>>
						<td class="wm_view_message_title">Charset:</td>
						<td>
							<select name="str_charset" id="strCharset" onchange="DoPost();" class="wm_view_message_select">
											<?php
											
												foreach ($CHARSETS as $value)
												{
													echo (Post::val('charset', '-1') == $value[0]) ?
														'<option value="'.$value[0].'" selected="selected" > '.$value[1].'</option>'."\r\n" :
														'<option value="'.$value[0].'" > '.$value[1].'</option>'."\r\n";
												}
											?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td id="td_attachments">
		<?php	
		
		$JSfilenameTrim = '';
		if ($message->msg->Attachments != null && $message->msg->Attachments->Count() > 0)
		{
			echo '<div id="attachments" class="wm_message_attachments">';
			
			$attachments = &$message->msg->Attachments;
			if ($attachments != null && $attachments->Count() > 0)
			{
				foreach (array_keys($attachments->Instance()) as $key)
				{
					$attachment = &$attachments->Get($key);
					$tempname = $message->msg->IdMsg.'-'.$key.'_'.$attachment->GetTempName();
					//$filename = ConvertUtils::ConvertEncoding($attachment->GetFilenameFromMime(), $GLOBALS[MailInputCharset], $message->account->GetUserCharset());
					$filename = $attachment->GetFilenameFromMime();
					$filesize = GetFriendlySize(strlen($attachment->MimePart->GetBinaryBody()));
										
					$fs = &new FileSystem(INI_DIR.'/temp', $message->account->Email, $message->account->Id);
					$attfolder = &new Folder($message->account->Id, -1, $_SESSION['attachtempdir']);
					$fs->SaveAttach($attachment, $attfolder, $tempname);
							
					$ContentType = ConvertUtils::GetContentTypeFromFileName($filename);
					
					$JSfilenameTrim .= '
					att = document.getElementById("at_'.$key.'");
					if (att.innerHTML.length > 16) 
							att.innerHTML = att.innerHTML.substring(0, 15) + \'&#8230;\';
					';
					
					echo '
					<div style="float: left;"><a href="attach.php?tn='.$tempname.'&filename='.urlencode($filename).'">
							<img src="./images/icons/'.GetAttachImg($filename).'" title="Click to download '.$filename.' ('.$filesize.')" /></a><br />
							<span id="at_'.$key.'" title="Click to download '.$filename.' ('.$filesize.')">'.$filename.'</span><br />';

					if (strpos($ContentType, 'image') !== false)
					{
						echo '<a href="#" onclick="PrevImg(\'view_image.php?tn='.$tempname.'\')">View</a>';
					}
					
					echo '</div>';
				}
				
			}
									
			echo '</div>
			</td>
			<td rowspan="3" id="td_vert_resizer"><div id="vert_resizer"></div></td>';
		}
		else 
		{
			echo '</td><td></td>';
		}
		?>
			
			<td id="td_message">
			<div id="message" class="wm_message">
			<?php
				$messageText = ($isHtml) ?	
				//'<div><span id="displayimg"><b>Images are not displayed.</b> <a href="#" onclick="SeeImages()" >Display images below?</a><br /><br /></span></div>'.
				ConvertUtils::ReplaceJSMethod($message->PrintHtmlBody(true)) : nl2br($message->PrintPlainBody());
				echo $messageText;
			?>
			</div>
			</td>		
		</tr>
		<?php
		if ($message->GetTypeOfMessage() > 2)
		{
			echo '<tr class="wm_lowtoolbar" id="lowtoolbar"><td colspan="3"><span class="wm_lowtoolbar_plain_html">';
			if (Post::val('plain', '-1') == -1 || Post::val('plain', '-1') == 3) 
			{
				echo '<span id="message_switcher"><a href="#" onclick="ChangeBody(2); return false;">Switch to Plain Text View</a></span>';
			}
			else 
			{
				echo '<span id="message_switcher"><a href="#" onclick="ChangeBody(3); return false;">Switch to Html View</a></span>';
			}	
			echo '</span></td></tr>';
			
			
		}
		?>
	</table>
	<script language="JavaScript">	
	function Init()
	{
		Headers = new CHeaders();
		Message = new CMessage();
		FullHeaders = new CFullHeadersViewer();

		<?php
		if ($message->msg->Attachments != null && $message->msg->Attachments->Count() > 0)
		{
			echo '
			Attachments = new CAttachments();
			VResizer = new CVResizer();';
		}
		?>
		
		ResizeElements('all');
	}
	Init();
	<?php
	echo $JSfilenameTrim;
	?>
	</script>
	
	
	</body>
	</html>	
	
<?php
} else $isNull = true;
			
			break;
			
		default:
		case 'none':
			$isNull = true; break;
	}
	
	
	if ($isNull)
	{
		
		$temp = ($isError) ? 'parent.InfoPanel._isError = true; parent.InfoPanel.SetInfo("This message has already been deleted from the mail server"); parent.InfoPanel.Show();': '';
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" />
<html>
<head>
	<title>iframe</title>
	<link rel="stylesheet" href="./skins/'.$account->DefaultSkin.'/styles.css" type="text/css" />
	<script language="JavaScript" type="text/javascript" src="./classic/_classes_message.js"></script>
	<script language="JavaScript" type="text/javascript" src="_functions.js"></script>
	<script>
	
	'.$temp.'
	
	function ResizeElements()
	{		
		document.body.scroll = "no";
		document.body.style.overflow = "hidden";

		var width = GetWidth();
		var height = GetHeight();
		
		Headers.width = width;
		Message.width = width;
		
		Headers.height = 60;
		Message.height = height - Headers.height;

		Headers.updateSize();
		Message.updateSize();
	}
	
	</script></head>
	<body onresize="ResizeElements();" style="background: #E9F2F8;" scroll="no" style="overflow: hidden;">
	<table class="wm_mail_container" id="wm_mail_container" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3" id="td_message_headers">
				<div  class="wm_message_headers" id="message_headers"></div>	
			</td>
		</tr>
		<tr>
			<td id="td_message">
				<div id="message" class="wm_message"></div>
			</td>		
		</tr>
	</table>
	<script language="JavaScript">	
	
	function Init()
	{
		Headers = new CHeaders();
		Message = new CMessage();
		ResizeElements();
	}
	Init();
	</script>
	</body>
</html>';
	}