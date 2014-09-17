<?php /* Smarty version 2.6.18, created on 2014-09-16 23:39:25
         compiled from addons/discussion/hooks/categories/detailed_content.post.tpl */ ?>
<?php
fn_preload_lang_vars(array('comments_and_reviews','discussion_title_category'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('comments_and_reviews', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion_manager/components/allow_discussion.tpl", 'smarty_include_vars' => array('prefix' => 'category_data','object_id' => $this->_tpl_vars['category_data']['category_id'],'object_type' => 'C','title' => fn_get_lang_var('discussion_title_category', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>