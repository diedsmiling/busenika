<?php /* Smarty version 2.6.18, created on 2014-09-24 21:48:48
         compiled from common_templates/image_verification.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_needs_image_verification', 'common_templates/image_verification.tpl', 3, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('image_verification_header','image_verification_label'));
?>
<?php  ob_start();  ?>
<?php if (fn_needs_image_verification("") == true): ?>
<h2 class="subheader"><?php echo fn_get_lang_var('image_verification_header', $this->getLanguage()); ?>
</h2>
<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LcDR9ISAAAAABI9jMIL0vwt1ZyKj829d7lAC5dP"></script>
	<noscript>
  		<iframe src="http://www.google.com/recaptcha/api/noscript?k=6LcDR9ISAAAAABI9jMIL0vwt1ZyKj829d7lAC5dP" height="300" width="500" frameborder="0"></iframe><br/>
  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>
<span class="hint_verification"><?php echo fn_get_lang_var('image_verification_label', $this->getLanguage()); ?>
. Если не можете различить символы на картинке, нажмите: <img src="http://www.google.com/recaptcha/api/img/red/refresh.gif"></span>
<?php endif; ?>
<?php  ob_end_flush();  ?>