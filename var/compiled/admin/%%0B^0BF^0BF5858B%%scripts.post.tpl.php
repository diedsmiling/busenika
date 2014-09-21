<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:39
         compiled from addons/buy_together/hooks/index/scripts.post.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'addons/buy_together/hooks/index/scripts.post.tpl', 3, false),)), $this); ?>
<?php  ob_start();  ?>
<?php echo smarty_function_script(array('src' => "addons/buy_together/js/func.js"), $this);?>


<script type="text/javascript">
// Extend core function
fn_register_hooks('buy_together', ['add_js_item', 'transfer_js_items']);

</script><?php  ob_end_flush();  ?>