<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:13
         compiled from common_templates/previewer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'common_templates/previewer.tpl', 5, false),array('function', 'script', 'common_templates/previewer.tpl', 10, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('close','click_on_images_text','press_esc_to'));
?>
<?php  ob_start();  ?>
<script type="text/javascript">
//<![CDATA[
lang.close = '<?php echo smarty_modifier_escape(fn_get_lang_var('close', $this->getLanguage()), 'javascript'); ?>
';
lang.click_on_images_text = '<?php echo smarty_modifier_escape(fn_get_lang_var('click_on_images_text', $this->getLanguage()), 'javascript'); ?>
';
lang.press_esc_to = '<?php echo smarty_modifier_escape(fn_get_lang_var('press_esc_to', $this->getLanguage()), 'javascript'); ?>
';
//]]>
</script>
<?php echo smarty_function_script(array('src' => "js/previewer.js"), $this);?>
<?php  ob_end_flush();  ?>