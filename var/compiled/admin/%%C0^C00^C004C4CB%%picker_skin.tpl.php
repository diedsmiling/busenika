<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:41
         compiled from pickers/picker_skin.tpl */ ?>
<?php
fn_preload_lang_vars(array('close','close'));
?>
<?php  ob_start();  ?>
<div class="popup-content cm-popup-box cm-picker hidden<?php if (! $this->_tpl_vars['no_bg_close']): ?> cm-bg-close<?php endif; ?>" id="picker_<?php echo $this->_tpl_vars['data_id']; ?>
">
	<div class="cm-popup-hor-resizer cm-left-resizer"></div>
	<div class="cm-popup-hor-resizer cm-right-resizer"></div>
	<div class="cm-popup-corner-resizer cm-nw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-ne-resizer"></div>
	<div class="cm-popup-corner-resizer cm-sw-resizer"></div>
	<div class="cm-popup-corner-resizer cm-se-resizer"></div>
	<div class="cm-popup-vert-resizer cm-top-resizer"></div>
	<div class="cm-popup-content-header">
		<div class="float-right">
			<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_close.gif" width="13" height="13" border="0" alt="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('close', $this->getLanguage()); ?>
" class="hand cm-popup-switch" />
		</div>
		<h3><?php echo $this->_tpl_vars['but_text']; ?>
:</h3>
	</div>
	<div class="cm-popup-content-footer">
		<?php echo $this->_tpl_vars['picker_content']; ?>

	</div>
	<div class="cm-popup-vert-resizer cm-bottom-resizer"></div>
</div>

<?php  ob_end_flush();  ?>