<?php


	class AccountDiv
	{
		/**
		 * @var array
		 */
		var $_accounts;
		
		/**
		 * @var int
		 */
		var $_account_id;
		
		/**
		 * @var string
		 */
		var $_skin;
		
		/**
		 * @param int $userId
		 * @return AccountDiv
		 */
		function AccountDiv($userId, $accountId, $skinName)
		{
			$this->_account_id = $accountId;
			$this->_skin = $skinName;
			
			$user = new CalendarUser();
			$acctArray = $user->SelectAccounts($userId);
			if (count($acctArray)> 0)
			{
				$this->_accounts =& $acctArray;
			}
			else 
			{
				$this->_accounts[$accountId] = '';
			}
		}
		
		/**
		 * @return int
		 */
		function Count()
		{
			return count($this->_accounts);
		}
		
		/**
		 * @return string
		 */
		function doTitle()
		{
			$class = ($this->Count() < 2) ? 'class="wm_hide"' : 'class="wm_accounts_arrow"';
	
			return '
				<span class="wm_accountslist_email" id="popup_replace_1">
					<a href="#" onclick="parent.HideCalendar(\'account\'); return false;">'.$this->_accounts[$this->_account_id].'</a>
				</span>			
				<span class="wm_accountslist_selection">
					<img '.$class.' id="popup_control_1" src="./skins/'.$this->_skin.'/menu/accounts_arrow.gif"
					onmousedown="this.src=\'./skins/'.$this->_skin.'/menu/accounts_arrow_down.gif\'" onmouseup="this.src=\'./skins/'.$this->_skin.'/menu/accounts_arrow_over.gif\'"
					onmouseover="this.src=\'./skins/'.$this->_skin.'/menu/accounts_arrow_over.gif\'" onmouseout="this.src=\'./skins/'.$this->_skin.'/menu/accounts_arrow.gif\'" />
				</span>	';
		}
		
		/**
		 * @return string
		 */
		function ToHideDiv()
		{
			$out = '';
			if ($this->Count() > 1)
			{
				$out .= '<div class="wm_hide" id="popup_menu_1" >';
				foreach ($this->_accounts as $id_acct => $email)
				{
					if ($this->_account_id != $id_acct)
					{
						$out .= '<div class="wm_account_item" onclick="parent.HideCalendar(\'account\', '.$id_acct.'); return false;"
								onmouseover="this.className=\'wm_account_item_over\';" onmouseout="this.className=\'wm_account_item\';">'.$email.'</div>';	
					}
				}
				$out.= '</div>';
			}
			return $out;
		}
	}