<?php /* Smarty version 2.6.18, created on 2014-09-16 23:16:43
         compiled from exception.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'exception.tpl', 7, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('administration_panel','access_denied','page_not_found','access_denied_text','page_not_found_text','go_back','go_to_the_admin_homepage'));
?>
<?php  ob_start();  ?>
<?php if (! $this->_tpl_vars['auth']['user_id']): ?>
	<span class="right"><span>&nbsp;</span></span>

	<h1 class="clear exception-header">
		<a href="<?php echo fn_url($this->_tpl_vars['config']['admin_index']); ?>
" class="float-left"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/<?php echo $this->_tpl_vars['manifest']['Signin_logo']['filename']; ?>
" width="<?php echo $this->_tpl_vars['manifest']['Signin_logo']['width']; ?>
" height="<?php echo $this->_tpl_vars['manifest']['Signin_logo']['height']; ?>
" border="0" alt="<?php echo $this->_tpl_vars['manifest']['Signin_logo']['alt']; ?>
" title="<?php echo $this->_tpl_vars['manifest']['Signin_logo']['alt']; ?>
" /></a>
		<span><?php echo fn_get_lang_var('administration_panel', $this->getLanguage()); ?>
</span>
	</h1>
<?php endif; ?>

<div class="exception-body login-content">

<h2><?php echo $this->_tpl_vars['exception_status']; ?>
</h2>

<h3>
	<?php if ($this->_tpl_vars['exception_status'] == '403'): ?>
		<?php echo fn_get_lang_var('access_denied', $this->getLanguage()); ?>

	<?php elseif ($this->_tpl_vars['exception_status'] == '404'): ?>
		<?php echo fn_get_lang_var('page_not_found', $this->getLanguage()); ?>

	<?php endif; ?>
</h3>

<div class="exception-content">
	<?php if ($this->_tpl_vars['exception_status'] == '403'): ?>
		<h4><?php echo fn_get_lang_var('access_denied_text', $this->getLanguage()); ?>
</h4>
	<?php elseif ($this->_tpl_vars['exception_status'] == '404'): ?>
		<h4><?php echo fn_get_lang_var('page_not_found_text', $this->getLanguage()); ?>
</h4>
	<?php endif; ?>
	
	<ul class="exception-menu">
		<li id="go_back"><a onclick="history.go(-1);"><?php echo fn_get_lang_var('go_back', $this->getLanguage()); ?>
</a></li>
		<li><a href="<?php echo fn_url($this->_tpl_vars['config']['admin_index']); ?>
"><?php echo fn_get_lang_var('go_to_the_admin_homepage', $this->getLanguage()); ?>
</a></li>
	</ul>

	<script type="text/javascript">
	//<![CDATA[
	<?php echo '
	 jQuery.each(jQuery.browser, function(i, val) {
		if ((i == \'opera\') && (val == true)) {
			if (history.length == 0) {
				$(\'#go_back\').hide();
			}
		} else {
			if (history.length == 1) {
				$(\'#go_back\').hide();
			}
		}
    });
	'; ?>

	//]]>
	</script>
</div>

</div>
<?php  ob_end_flush();  ?>