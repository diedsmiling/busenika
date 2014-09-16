<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:45
         compiled from addons/discussion/hooks/products/detailed_content.post.tpl */ ?>
<?php
fn_preload_lang_vars(array('comments_and_reviews','discussion_title_product'));
?>

<fieldset>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('comments_and_reviews', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion_manager/components/allow_discussion.tpl", 'smarty_include_vars' => array('prefix' => 'product_data','object_id' => $this->_tpl_vars['product_data']['product_id'],'object_type' => 'P','title' => fn_get_lang_var('discussion_title_product', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</fieldset>