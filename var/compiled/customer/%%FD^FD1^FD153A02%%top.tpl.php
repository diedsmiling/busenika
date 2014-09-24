<?php /* Smarty version 2.6.18, created on 2014-09-23 21:20:59
         compiled from top.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'top.tpl', 6, false),array('modifier', 'sizeof', 'top.tpl', 22, false),array('modifier', 'fn_link_attach', 'top.tpl', 24, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('localization'));
?>

<div class="header-helper-container">
	<div class="wrapper">
		<div class="logo-image">
			<a href="<?php echo fn_url(""); ?>
"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/korzin.net_05.png" border="0"/></a>
		</div>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "top_quick_links.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>

<div class="top-tools-container">
	<div class="wrapper">	
		<div class="cont-width">
			<div class="leftwards"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
			<div class="rightwards">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/checkout/components/cart_status.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>	

				<?php if (sizeof($this->_tpl_vars['localizations']) > 1): ?>			
				<!--dynamic:localizations-->				
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_object.tpl", 'smarty_include_vars' => array('style' => 'graphic','link_tpl' => fn_link_attach($this->_tpl_vars['config']['current_url'], "lc="),'items' => $this->_tpl_vars['localizations'],'selected_id' => @CART_LOCALIZATION,'display_icons' => false,'key_name' => 'localization','text' => fn_get_lang_var('localization', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<!--/dynamic-->	
				<?php endif; ?>			
				<!--dynamic:languages-->			
				<?php if (sizeof($this->_tpl_vars['languages']) > 1): ?>				
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_object.tpl", 'smarty_include_vars' => array('style' => 'graphic','link_tpl' => fn_link_attach($this->_tpl_vars['config']['current_url'], "sl="),'items' => $this->_tpl_vars['languages'],'selected_id' => @CART_LANGUAGE,'display_icons' => true,'key_name' => 'name','language_var_name' => 'sl')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
				<!--/dynamic-->			
				<?php if (sizeof($this->_tpl_vars['currencies']) > 1): ?>			
				<!--dynamic:currency-->				
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_object.tpl", 'smarty_include_vars' => array('style' => 'graphic','link_tpl' => fn_link_attach($this->_tpl_vars['config']['current_url'], "currency="),'items' => $this->_tpl_vars['currencies'],'selected_id' => $this->_tpl_vars['secondary_currency'],'display_icons' => false,'key_name' => 'description')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<!--/dynamic-->
				<?php endif; ?>
			</div>
		</div>
	</div>