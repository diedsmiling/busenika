<?php
$null = null;
$accountPerPage = 20;
$cnomber = 7;

if (isset($_GET['page']))
{
	$_SESSION['madm_page'] = abs((int) $_GET['page']);
}
$page = isset($_SESSION['madm_page']) ? (int) $_SESSION['madm_page'] : 1;

if (isset($_GET['order']))
{
	$_SESSION['madm_order'] = $_GET['order'];
}
$order = isset($_SESSION['madm_order']) ? $_SESSION['madm_order'] : 'id_user';

if (isset($_GET['ordtype']))
{
	$_SESSION['madm_ordtype'] = $_GET['ordtype'];
}
$ordtype = isset($_SESSION['madm_ordtype']) ? (int) $_SESSION['madm_ordtype'] : 1;

if (isset($_GET['search_text']) && strlen($_GET['search_text']) > 0)
{
	 $_SESSION['madm_search_text'] = $_GET['search_text'];
	 $_SESSION['madm_page'] = 1;
	 $page = 1;
}
elseif (isset($_GET['search_text']) && strlen($_GET['search_text']) == 0)
{
	unset($_SESSION['madm_search_text']);
}

$searchText = isset($_SESSION['madm_search_text']) ? $_SESSION['madm_search_text'] : '';
if (isset($_GET['clear']))
{
	unset($_SESSION['madm_search_text']);
	$searchText =  '';	
}

$ordimg = ($ordtype == 1) ? 'admin_arrow_1.gif' : 'admin_arrow_2.gif'; 
$ordimg = $skinPath.'/'.$ordimg;

?>
<!-- [start center] -->
<table class="wm_admin_center_min" width="100%">
	<tr>
		<td>
	<table width="98%" class="wm_user_table" id="wm_user_table">
		<tr>
			<th onclick="document.location.replace('?mode=wm_users&order=id_user&ordtype=<?php echo ($order == 'id_user' && $ordtype == 1) ? '0' : '1';  ?>')";
			<?php echo ($order == 'id_user')?'style="background-image: url('.$ordimg.'); background-repeat: no-repeat; background-position: right;"':'';?>>
			<a href="?mode=wm_users&order=id_user&ordtype=<?php echo ($order == 'id_user' && $ordtype == 1) ? '0' : '1'; ?>"><font color="Black"><nobr>User Id</nobr></font></a>
			</th>
			<th onclick="document.location.replace('?mode=wm_users&order=email&ordtype=<?php echo ($order == 'email' && $ordtype == 1) ? '0' : '1';?>')";
			<?php echo ($order == 'email')?'style="background-image: url('.$ordimg.'); background-repeat: no-repeat; background-position: right;"':'';?>>
			<a href="?mode=wm_users&order=email&ordtype=<?php echo ($order == 'email' && $ordtype == 1) ? '0' : '1'; ?>"><font color="Black">Email</font></a>
			</th>
			<th onclick="document.location.replace('?mode=wm_users&order=last_login&ordtype=<?php echo ($order == 'last_login' && $ordtype == 1) ? '0' : '1'; ?>')";
			<?php echo ($order == 'last_login')?'style="background-image: url('.$ordimg.'); background-repeat: no-repeat;	background-position: right;"':'';?>>
			<a href="?mode=wm_users&order=last_login&ordtype=<?php echo ($order == 'last_login' && $ordtype == 1) ? '0' : '1'; ?>"><font color="Black">Last Login</font></a>
			</th>
			<th onclick="document.location.replace('?mode=wm_users&order=logins_count&ordtype=<?php echo ($order == 'logins_count' && $ordtype == 1) ? '0' : '1'; ?>')";
			<?php echo ($order == 'logins_count')?'style="background-image: url('.$ordimg.'); background-repeat: no-repeat;	background-position: right;"':''; ?>>
			<a href="?mode=wm_users&order=logins_count&ordtype=<?php echo ($order == 'logins_count' && $ordtype == 1) ? '0' : '1'; ?>"><font color="Black">Logins</font></a>
			</th>
			<th onclick="document.location.replace('?mode=wm_users&order=mail_inc_host&ordtype=<?php echo ($order == 'mail_inc_host' && $ordtype == 1) ? '0' : '1'; ?>')";
			<?php echo ($order == 'mail_inc_host')?'style="background-image: url('.$ordimg.'); background-repeat: no-repeat;	background-position: right;"':'';?>>
			<a href="?mode=wm_users&order=mail_inc_host&ordtype=<?php echo ($order == 'mail_inc_host' && $ordtype == 1) ? '0' : '1'; ?>"><font color="Black">Incoming Server</font></a>
			</th>
			<th onclick="document.location.replace('?mode=wm_users&order=mail_out_host&ordtype=<?php echo ($order == 'mail_out_host' && $ordtype == 1) ? '0' : '1'; ?>')";
			<?php echo ($order == 'mail_out_host')?'style="background-image: url('.$ordimg.'); background-repeat: no-repeat;	background-position: right;"':'';?>>
			<a href="?mode=wm_users&order=mail_out_host&ordtype=<?php echo ($order == 'mail_out_host' && $ordtype == 1) ? '0' : '1'; ?>"><font color="Black">Outgoing Server</font></a>
			</th>
			<th onclick="document.location.replace('?mode=wm_users&order=mailbox_size&ordtype=<?php echo ($order == 'mailbox_size' && $ordtype == 1) ? '0' : '1'; ?>')";
			<?php echo ($order == 'mailbox_size')?'style="background-image: url('.$ordimg.'); background-repeat: no-repeat;	background-position: right;"':'';?>>
			<a href="?mode=wm_users&order=mailbox_size&ordtype=<?php echo ($order == 'mailbox_size' && $ordtype == 1) ? '0' : '1'; ?>"><font color="Black">Used <nobr>space,
			<?php echo ($settings->EnableMailboxSizeLimit) ? '%' : 'Kb';?></nobr></font></a>
			<th width="70" style="cursor: default;">Action</th>
		</tr>
<?php

$dbStorage = &DbStorageCreator::CreateDatabaseStorage($null);
$account_count = 0;

if ($dbStorage->Connect())
{
	$account_count = $dbStorage->SelectAccountsCount($searchText);
	$user_count = null;
	if (strlen($searchText) == 0)
	{
		$user_count = $dbStorage->SelectUsersCount();
	}
	if (ceil($account_count/$accountPerPage) < $page)
	{
		$page = 1;
	}
	
	$AccArray = &$dbStorage->SelectAllAccounts($page, $accountPerPage, $order, $ordtype, $searchText);
	
	$bool = false;
	$c = count($AccArray);
	
	for ($i = 0; $i < $c; $i++)
	{
		$accountArray = &$AccArray[$i];

		echo ($bool) ? '<tr class="even">' : '<tr>';
		$bool = !$bool;

		echo '<td align="center">'.$accountArray['IdUser'].'</td>';
		echo '<td>';
		echo ($accountArray['DefAcct'] == '1') ? '<b>'.$accountArray['Email'].'</b>' : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$accountArray['Email'];
		echo '</td>';
		echo '<td>'.$accountArray['LastLogin'].'</td>';
		echo '<td align="center">'.$accountArray['LoginsCount'].'</td>';
		echo '<td>'.$accountArray['MailIncHost'].'</td>';
		echo '<td>'.$accountArray['MailOutHost'].'</td>';
		
		if ($settings->EnableMailboxSizeLimit)
		{
			if ($accountArray['MailboxLimit'])
			{
				$alttext = 'Account is using  '.round($accountArray['MailboxSize']/$accountArray['MailboxLimit']*100).'% ('.GetFriendlySize($accountArray['MailboxSize']).') of user '.GetFriendlySize($accountArray['MailboxLimit']);
				$used_acc = round($accountArray['MailboxSize']/$accountArray['MailboxLimit']*100);
				$used_acc = ($used_acc >= 100) ? 100 : $used_acc;
				$used_all = round($accountArray['UserMailboxSize']/$accountArray['MailboxLimit']*100);
				$used_all = ($used_all >= 100) ? 100 : $used_all;
				$used_all = $used_all - $used_acc;
				
				$used_all_clr = '#B7CEDE;';
				$used_acc_clr = '#7E9BAF;';
				
				echo '<td align="center">
				<div class="wm_progressbar" style="border: solid 1px #7E9BAF;" title="'.ConvertUtils::AttributeQuote($alttext).'"><div class="wm_progressbar_used" style="background: '.$used_acc_clr.' width: '. $used_acc .'px;"></div>
				<div class="wm_progressbar_used" style="background: '.$used_all_clr.' width: '. $used_all .'px;"></div>
				</div></td>';   
			}
			else 
			{
				$alttext = 'User is using  100% ('.GetFriendlySize($accountArray['MailboxSize']).') of his(her) '.GetFriendlySize($accountArray['MailboxLimit']);
				echo '<td align="center"><div class="wm_progressbar" style="border: solid 1px #7E9BAF;" title="'.ConvertUtils::AttributeQuote($alttext).'"><div class="wm_progressbar_used" style="background: #7E9BAF; width: 100px;"></div></div></td>';
			}
		}
		else 
		{
			echo '<td align="right">'.round($accountArray['MailboxSize']/1024).'</td>';   
		}	
		
		echo '<td align="center">&nbsp;&nbsp;<a href="?mode=wm_edit&uid='.$accountArray['Id'].'">Edit</a>&nbsp;&nbsp;<a href="?mode=wm_delete&uid='.$accountArray['Id'].'" onclick="return confirm(\'Are you sure?\');">Delete</a></td>';
		echo '</tr>'."\r\n";
	}
	if ($c == 0)
	{
		echo '<tr><td align="center" height="30">No Records</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
	}
}
else 
{
	echo '<tr><td align="center" height="30">Bad connect</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
}
?>
	</table>
	<table width="98%">
		<tr>
			<td valign="middle" align="left">
<?php
	if ($searchText && strlen($searchText) > 0)
	{
		echo '<br />&nbsp;&nbsp;&nbsp;<b>Search result:</b> '.$account_count.' account(s)';
	}
	else 
	{
		echo '<br />&nbsp;&nbsp;&nbsp;<b>Total:</b> '.$account_count.' account(s)';
		if ($user_count !== null)
		{
			echo '/ '.$user_count.' user(s)';
		}
	}
?>
			</td>
		</tr>
		<tr>
			<td valign="middle" align="center">
				<div style="float: none;"><br />
<?php

	if ($account_count > $accountPerPage && $cnomber > 0)
	{
		$arr = array($page => '');
		$step = 1;
		$start = 1;
		$end = ceil($account_count/$accountPerPage);
		while (count($arr) < $cnomber)
		{
			$prev = $page - $step;
			$next = $page + $step;
			
			if ($prev >= $start)
			{
				$arr[$prev] = '';
			}
			
			if ($next <= $end)
			{
				$arr[$next] = '';
			}
			
			++$step;
			if (count($arr) >= $end)
			{
				break;
			}
		}
		
		$arr = array_keys($arr);
		sort($arr);
		
		if (count($arr) > 0)
		{
			$first = (int) $arr[0];
			$last = (int) $arr[count($arr) - 1];
			
			if (!in_array(1, $arr) && $first - 1 != 1)
			{
				echo ' <a href="?mode=wm_users&page=1">&#27;</a> ';
			}
			
			if ($first > 1)
			{
				echo ' <a href="?mode=wm_users&page='.($first - 1).'">...</a> ';
			}
			
			foreach ($arr as $nom)
			{
				if ($nom == $page)
				{
					echo ' <b>'.$page.'</b> ';
				}
				else
				{
					echo ' <a href="?mode=wm_users&page='.$nom.'">'.$nom.'</a> ';
				}
			}
			
			if ($last < $end)
			{
				echo ' <a href="?mode=wm_users&page='.($last + 1).'">...</a> ';
			}
			
			if (!in_array($end, $arr)  && ($last + 1) != $end)
			{
				echo ' <a href="?mode=wm_users&page='.$end.'">&#26;</a> ';
			}
		}
	}

?>
				</div>
					
				<div style="float: left;">
					<form action="" method="GET">
						<input type="hidden" name="mode" class="wm_input" value="wm_edit" />
						<input type="hidden" name="uid" class="wm_input" value="-1" />
						<input type="submit" value="Create User"  class="wm_button" style="width: 150px" />
					</form>		
				</div>
				
				<div style="float: right;">
					<form action="" method="GET">
						<input type="hidden" name="mode" class="wm_input" value="wm_users" />
						<input type="text" name="search_text" class="wm_input" size="30" value="<?php echo ConvertUtils::AttributeQuote($searchText); ?>" />
						<input type="submit" value="Search"  class="wm_button" />
					</form>		
				</div>	
			</td>	
		</tr>	
	</table>	
	
	
		</td>
	</tr>
</table>
<!-- [end center] -->