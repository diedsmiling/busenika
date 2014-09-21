<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:14
         compiled from addons/banners/blocks/original.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'addons/banners/blocks/original.tpl', 7, false),array('modifier', 'unescape', 'addons/banners/blocks/original.tpl', 12, false),)), $this); ?>

<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['banner']):
?>
	<?php if ($this->_tpl_vars['banner']['type'] == 'G' && $this->_tpl_vars['banner']['main_pair']['image_id']): ?>
	<div class="ad-container center">
		<?php if ($this->_tpl_vars['banner']['url'] != ""): ?><a href="<?php echo fn_url($this->_tpl_vars['banner']['url']); ?>
" <?php if ($this->_tpl_vars['banner']['target'] == 'B'): ?>target="_blank"<?php endif; ?>><?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('images' => $this->_tpl_vars['banner']['main_pair'],'object_type' => 'common')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php if ($this->_tpl_vars['banner']['url'] != ""): ?></a><?php endif; ?>
	</div>
	<?php else: ?>
		<?php echo smarty_modifier_unescape($this->_tpl_vars['banner']['description']); ?>

	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>