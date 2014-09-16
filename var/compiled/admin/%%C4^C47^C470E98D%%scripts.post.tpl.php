<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:40
         compiled from addons/news_and_emails/hooks/index/scripts.post.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'addons/news_and_emails/hooks/index/scripts.post.tpl', 3, false),)), $this); ?>
<?php  ob_start();  ?>
<?php echo smarty_function_script(array('src' => "addons/news_and_emails/js/func.js"), $this);?>

<script type="text/javascript">

// Extend core function
fn_register_hooks('news_and_emails', ['add_js_item']);

</script><?php  ob_end_flush();  ?>