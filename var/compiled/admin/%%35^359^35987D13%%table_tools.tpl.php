<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:41
         compiled from common_templates/table_tools.tpl */ ?>
<?php
fn_preload_lang_vars(array('select_all','unselect_all'));
?>
<?php  ob_start();  ?>
<div class="table-tools">
	<a href="<?php echo $this->_tpl_vars['href']; ?>
" name="check_all" class="cm-check-items cm-on underlined"><?php echo fn_get_lang_var('select_all', $this->getLanguage()); ?>
</a>|
	<a href="<?php echo $this->_tpl_vars['href']; ?>
" name="check_all" class="cm-check-items cm-off underlined"><?php echo fn_get_lang_var('unselect_all', $this->getLanguage()); ?>
</a>
	</div>
<?php  ob_end_flush();  ?>