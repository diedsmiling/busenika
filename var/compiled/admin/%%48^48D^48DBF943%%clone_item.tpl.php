<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:44
         compiled from buttons/clone_item.tpl */ ?>
<?php
fn_preload_lang_vars(array('clone_this_item','clone_this_item'));
?>
<?php  ob_start();  ?>
<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_clone.gif" width="13" height="18" border="0" name="clone" id="<?php echo $this->_tpl_vars['item_id']; ?>
" title="<?php echo fn_get_lang_var('clone_this_item', $this->getLanguage()); ?>
" alt="<?php echo fn_get_lang_var('clone_this_item', $this->getLanguage()); ?>
" onclick="<?php echo $this->_tpl_vars['but_onclick']; ?>
" class="hand" align="top" /><?php  ob_end_flush();  ?>