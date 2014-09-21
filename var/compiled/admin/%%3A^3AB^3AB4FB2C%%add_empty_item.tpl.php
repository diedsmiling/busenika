<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:44
         compiled from buttons/add_empty_item.tpl */ ?>
<?php
fn_preload_lang_vars(array('add_empty_item','add_empty_item'));
?>
<?php  ob_start();  ?>
<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_add.gif" width="13" height="18" border="0" name="add" id="<?php echo $this->_tpl_vars['item_id']; ?>
" alt="<?php echo fn_get_lang_var('add_empty_item', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('add_empty_item', $this->getLanguage()); ?>
" onclick="<?php echo $this->_tpl_vars['but_onclick']; ?>
" class="hand" align="top" /><?php  ob_end_flush();  ?>