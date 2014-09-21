<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:12
         compiled from top_quick_links.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'hook', 'top_quick_links.tpl', 2, false),array('modifier', 'fn_url', 'top_quick_links.tpl', 5, false),)), $this); ?>
<?php  ob_start();  ?><?php $this->_tag_stack[] = array('hook', array('name' => "index:top_links")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<p class="quick-links">&nbsp;
	<?php $_from = $this->_tpl_vars['quick_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['link']):
?>
		<a href="<?php echo fn_url($this->_tpl_vars['link']['param']); ?>
"><?php echo $this->_tpl_vars['link']['descr']; ?>
</a>
	<?php endforeach; endif; unset($_from); ?>
</p>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php  ob_end_flush();  ?>