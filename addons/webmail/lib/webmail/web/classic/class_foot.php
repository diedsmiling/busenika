<?php

	if (!defined('WM_ROOTPATH')) define('WM_ROOTPATH', (dirname(__FILE__).'/../'));
	
	
class FootPanel
{

	/**
	 * @var PageBuilder
	 */
	var $pagebuilder;
	
	/**
	 * @var string;
	 */
	var $text = '';
	
	/**
	 * @param PageBuilder $pagebuilder
	 * @return FootPanel
	 */
	function FootPanel(&$pagebuilder)
	{
		$this->pagebuilder = &$pagebuilder;
	}
	
	/**
	 * @return string
	 */
	function ToHTML()
	{
		$screen = $this->pagebuilder->_proc->sArray[SCREEN];
		$prew = ($this->pagebuilder->_proc->account->ViewMode == VIEW_MODE_PREVIEW_PANE || $this->pagebuilder->_proc->account->ViewMode == VIEW_MODE_PREVIEW_PANE_NO_IMG);
		$copy = @file_get_contents('inc.footer.php');
		$copyclass = 'wm_copyright';
		if ($screen == SCREEN_FULLSCREEN || ($screen == SCREEN_MAILBOX && $prew) || $screen == SCREEN_CONTACTS)
		{
			$copyclass = 'wm_hide';	
		}
		
		$this->text .= '<div class="'.$copyclass.'" id="copyright">'.$copy.'</div></div>'.$this->pagebuilder->_js->_iniTextToHtml();

		if (isset($_SESSION[INFORMATION]) && strlen($_SESSION[INFORMATION]) > 0) 
		{
			$this->text .= (isset($_SESSION[ISINFOERROR]) && $_SESSION[ISINFOERROR] = true) ? 
					'<script>InfoPanel.Class("wm_error_information", "", "error.gif"); InfoPanel.Show();</script>' :
					'<script>InfoPanel.Class("wm_information", "wm_hide", "info.gif");  InfoPanel.Show();</script>';
			$_SESSION[INFORMATION] = '';
			$_SESSION[ISINFOERROR] = false;
		}
		else 
		{
			$this->text .= '
			<script>
			if (InfoPanel._isError) {
				InfoPanel.Class("wm_error_information", "wm_info_image", "error.gif");
				InfoPanel.Show();
			} else {
				InfoPanel.Hide();
			}
			</script>';
		}

		$this->text .= '<iframe name="session_saver" id="session_saver" src="session-saver.php" class="wm_hide"></iframe></body></html>';
		
		return $this->text;
	}
	
}
