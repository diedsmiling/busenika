<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from buttons/search_go.tpl */ ?>
<?php
fn_preload_lang_vars(array('search','search'));
?>
<?php  ob_start();  ?>
<input type="hidden" name="dispatch" value="<?php echo $this->_tpl_vars['but_name']; ?>
" />
<input type="image" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/search_go.gif" class="search-go" alt="<?php echo fn_get_lang_var('search', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('search', $this->getLanguage()); ?>
" /><?php  ob_end_flush();  ?>