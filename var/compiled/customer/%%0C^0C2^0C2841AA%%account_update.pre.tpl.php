<?php /* Smarty version 2.6.18, created on 2014-09-22 22:26:38
         compiled from addons/news_and_emails/hooks/profiles/account_update.pre.tpl */ ?>
<?php
fn_preload_lang_vars(array('mailing_lists','text_signup_for_subscriptions','txt_format','html_format'));
?>

<?php if ($this->_tpl_vars['page_mailing_lists']): ?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('mailing_lists', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<p><?php echo fn_get_lang_var('text_signup_for_subscriptions', $this->getLanguage()); ?>
</p>

	<?php $_from = $this->_tpl_vars['page_mailing_lists']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['list']):
?>
		<div class="select-field">
			<input type="hidden" name="mailing_lists[<?php echo $this->_tpl_vars['list']['list_id']; ?>
]" value="0" />
			<input id="profile_mailing_list_<?php echo $this->_tpl_vars['list']['list_id']; ?>
" type="checkbox" name="mailing_lists[<?php echo $this->_tpl_vars['list']['list_id']; ?>
]" value="1" <?php if ($this->_tpl_vars['user_mailing_lists'][$this->_tpl_vars['list']['list_id']]): ?>checked="checked"<?php endif; ?> class="checkbox" /><label for="profile_mailing_list_<?php echo $this->_tpl_vars['list']['list_id']; ?>
"><?php echo $this->_tpl_vars['list']['object']; ?>
</label>
		</div>
	<?php endforeach; endif; unset($_from); ?>

	<div class="select-field">
	<select name="newsletter_format">
		<option value="<?php echo @NEWSLETTER_FORMAT_TXT; ?>
" <?php if ($this->_tpl_vars['newsletter_format'] == @NEWSLETTER_FORMAT_TXT): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('txt_format', $this->getLanguage()); ?>
</option>
		<option value="<?php echo @NEWSLETTER_FORMAT_HTML; ?>
" <?php if ($this->_tpl_vars['newsletter_format'] == @NEWSLETTER_FORMAT_HTML): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('html_format', $this->getLanguage()); ?>
</option>
	</select>
	</div>
<?php endif; ?>