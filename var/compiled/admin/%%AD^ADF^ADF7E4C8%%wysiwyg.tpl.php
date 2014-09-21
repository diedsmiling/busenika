<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:44
         compiled from common_templates/wysiwyg.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'lower', 'common_templates/wysiwyg.tpl', 7, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('edit_in_visual_editor','view_source'));
?>

<?php if (! $this->_smarty_vars['capture']['wysiwyg']): ?>
<div class="script-holder"></div>
<script type="text/javascript">
//<![CDATA[
tiny_lang = '<?php echo smarty_modifier_lower(@CART_LANGUAGE); ?>
';

var node = document.createElement("script");
node.src = '<?php echo $this->_tpl_vars['config']['current_path']; ?>
/lib/tinymce/tiny_mce.js';
$('.script-holder').get(0).appendChild(node);
node = document.createElement("script");
node.src = '<?php echo $this->_tpl_vars['config']['current_path']; ?>
/lib/tinymce/tiny_mce_init.js';
$('.script-holder').get(0).appendChild(node);
node = null;
//]]>
</script>
<?php ob_start(); ?>Y<?php $this->_smarty_vars['capture']['wysiwyg'] = ob_get_contents(); ob_end_clean(); ?>
<?php endif; ?>

<p>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('edit_in_visual_editor', $this->getLanguage()),'but_onclick' => "jQuery.openEditor(this.id.str_replace('on_b', ''));",'but_role' => 'simple','but_meta' => "text-button cm-combination",'but_id' => "on_b".($this->_tpl_vars['id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('view_source', $this->getLanguage()),'but_onclick' => "jQuery.openEditor(this.id.str_replace('off_b', ''));",'but_role' => 'simple','but_meta' => "text-button cm-combination hidden",'but_id' => "off_b".($this->_tpl_vars['id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</p>