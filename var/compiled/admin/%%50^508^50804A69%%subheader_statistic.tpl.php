<?php /* Smarty version 2.6.18, created on 2014-09-18 23:25:39
         compiled from common_templates/subheader_statistic.tpl */ ?>
<?php
fn_preload_lang_vars(array('hide','hide','close','close'));
?>
<?php  ob_start();  ?>
<h2>
	<span class="float-right hidden">
		<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_hide.gif" width="13" height="13" border="0" alt="<?php echo fn_get_lang_var('hide', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('hide', $this->getLanguage()); ?>
" />
		<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_close.gif" width="13" height="13" border="0" alt="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" />
	</span>
	<?php echo $this->_tpl_vars['title']; ?>

</h2><?php  ob_end_flush();  ?>