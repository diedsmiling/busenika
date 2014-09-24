<?php /* Smarty version 2.6.18, created on 2014-09-23 22:33:34
         compiled from addons/myob/hooks/profiles/list_tools.post.tpl */ ?>
<?php
fn_preload_lang_vars(array('export_to_myob'));
?>
<?php  ob_start();  ?><?php if ($this->_tpl_vars['search']['user_type'] == 'C'): ?>
	<li><a class="cm-process-items" name="dispatch[myob_export.export_profiles]" rev="userlist_form"><?php echo fn_get_lang_var('export_to_myob', $this->getLanguage()); ?>
</a></li>
<?php endif; ?><?php  ob_end_flush();  ?>