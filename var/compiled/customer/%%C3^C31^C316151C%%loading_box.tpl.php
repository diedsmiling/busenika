<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:56
         compiled from common_templates/loading_box.tpl */ ?>
<?php
fn_preload_lang_vars(array('loading'));
?>
<?php  ob_start();  ?>
<div id="ajax_loading_box" class="ajax-loading-box"><div class="right-inner-loading-box"><div id="ajax_loading_message" class="ajax-inner-loading-box"><?php echo fn_get_lang_var('loading', $this->getLanguage()); ?>
</div></div></div>
<?php  ob_end_flush();  ?>