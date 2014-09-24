<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:16
         compiled from buttons/remove_item.tpl */ ?>
<?php
fn_preload_lang_vars(array('remove_this_item','remove_this_item','remove_this_item','remove_this_item'));
?>
<?php  ob_start();  ?>
<?php if (! $this->_tpl_vars['simple']): ?>
<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_delete_disabled.gif" width="12" height="18" border="0" name="remove" id="<?php echo $this->_tpl_vars['item_id']; ?>
" alt="<?php echo fn_get_lang_var('remove_this_item', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('remove_this_item', $this->getLanguage()); ?>
" class="hand<?php if ($this->_tpl_vars['only_delete'] == 'Y'): ?> hidden<?php endif; ?>" align="top" />
<?php endif; ?>
<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_delete.gif" width="12" height="18" border="0" name="remove_hidden" id="<?php echo $this->_tpl_vars['item_id']; ?>
" alt="<?php echo fn_get_lang_var('remove_this_item', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('remove_this_item', $this->getLanguage()); ?>
"<?php if ($this->_tpl_vars['but_onclick']): ?> onclick="<?php echo $this->_tpl_vars['but_onclick']; ?>
"<?php endif; ?> class="hand<?php if (! $this->_tpl_vars['simple'] && $this->_tpl_vars['only_delete'] != 'Y'): ?> hidden<?php endif; ?><?php if ($this->_tpl_vars['but_class']): ?> <?php echo $this->_tpl_vars['but_class']; ?>
<?php endif; ?>" align="top" />
<?php  ob_end_flush();  ?>