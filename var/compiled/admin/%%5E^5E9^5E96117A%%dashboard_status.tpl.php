<?php /* Smarty version 2.6.18, created on 2014-09-18 23:25:39
         compiled from addons/discussion/views/index/components/dashboard_status.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'addons/discussion/views/index/components/dashboard_status.tpl', 5, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('approved','disapproved'));
?>

<div class="float-right nowrap right" id="post_<?php echo $this->_tpl_vars['post']['post_id']; ?>
">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_popup.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['post']['post_id'],'status' => $this->_tpl_vars['post']['status'],'hidden' => "",'object_id_name' => 'post_id','table' => 'discussion_posts','items_status' => "A: ".(fn_get_lang_var('approved', $this->getLanguage())).", D: ".(fn_get_lang_var('disapproved', $this->getLanguage())))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<strong><?php echo smarty_modifier_date_format($this->_tpl_vars['post']['timestamp'], ($this->_tpl_vars['settings']['Appearance']['date_format']).", ".($this->_tpl_vars['settings']['Appearance']['time_format'])); ?>
</strong>&nbsp;-&nbsp;
<!--post_<?php echo $this->_tpl_vars['post']['post_id']; ?>
--></div>