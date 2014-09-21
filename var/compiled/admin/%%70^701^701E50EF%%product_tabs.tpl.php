<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:44
         compiled from views/products/components/product_tabs.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_query_remove', 'views/products/components/product_tabs.tpl', 6, false),array('modifier', 'escape', 'views/products/components/product_tabs.tpl', 8, false),array('block', 'hook', 'views/products/components/product_tabs.tpl', 13, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('editing_block','new_block','add_block','new_group','add_group'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/components/scripts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('text' => fn_get_lang_var('editing_block', $this->getLanguage()),'content' => $this->_tpl_vars['content'],'id' => 'edit_block_picker','edit_picker' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->assign('cur_url', fn_query_remove($this->_tpl_vars['config']['current_url'], 'selected_section'), false); ?>
<?php $this->assign('cur_url', ($this->_tpl_vars['cur_url'])."&selected_section=blocks", false); ?>
<?php $this->assign('c_url', smarty_modifier_escape($this->_tpl_vars['cur_url'], 'url'), false); ?>

<div class="block-manager products-block-manager">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_group_element.tpl", 'smarty_include_vars' => array('blocks_target' => 'top','main_class' => "",'redirect_url' => $this->_tpl_vars['c_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<div class="clear">
	<?php $this->_tag_stack[] = array('hook', array('name' => "block_manager:columns")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_group_element.tpl", 'smarty_include_vars' => array('blocks_target' => 'left','main_class' => "float-left",'redirect_url' => $this->_tpl_vars['c_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_group_element.tpl", 'smarty_include_vars' => array('blocks_target' => 'central','main_class' => "float-left",'redirect_url' => $this->_tpl_vars['c_url'],'non_editable' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_group_element.tpl", 'smarty_include_vars' => array('blocks_target' => 'right','main_class' => "float-left",'redirect_url' => $this->_tpl_vars['c_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	</div>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_tabs_group_element.tpl", 'smarty_include_vars' => array('blocks_target' => 'bottom','main_class' => "",'redirect_url' => $this->_tpl_vars['c_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/components/sortable_scripts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/update.tpl", 'smarty_include_vars' => array('add_block' => true,'block_type' => 'B','block' => null,'redirect_url' => $this->_tpl_vars['cur_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $this->_smarty_vars['capture']['add_new_block'] = ob_get_contents(); ob_end_clean(); ?>
<?php ob_start(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/block_manager/update.tpl", 'smarty_include_vars' => array('add_block' => true,'block_type' => 'G','block' => null,'redirect_url' => $this->_tpl_vars['cur_url'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $this->_smarty_vars['capture']['add_new_group'] = ob_get_contents(); ob_end_clean(); ?>

<div class="buttons-container">
	<div class="float-right">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_block','text' => fn_get_lang_var('new_block', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['add_new_block'],'link_text' => fn_get_lang_var('add_block', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_group','text' => fn_get_lang_var('new_group', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['add_new_group'],'link_text' => fn_get_lang_var('add_group', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
</div>