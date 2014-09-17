<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:56
         compiled from meta.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'hook', 'meta.tpl', 3, false),array('modifier', 'lower', 'meta.tpl', 9, false),array('modifier', 'html_entity_decode', 'meta.tpl', 10, false),array('modifier', 'default', 'meta.tpl', 10, false),)), $this); ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "index:meta")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['display_base_href']): ?>
<base href="<?php echo $this->_tpl_vars['config']['current_location']; ?>
/" />
<?php endif; ?>
<meta name='yandex-verification' content='798ca436c6ebed88' />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo @CHARSET; ?>
" />
<meta http-equiv="Content-Language" content="<?php echo smarty_modifier_lower(@CART_LANGUAGE); ?>
" />
<meta name="description" content="<?php echo smarty_modifier_default(html_entity_decode($this->_tpl_vars['meta_description']), @$this->_tpl_vars['location_data']['meta_description']); ?>
" />
<meta name="keywords" content="<?php echo smarty_modifier_default(@$this->_tpl_vars['meta_keywords'], @$this->_tpl_vars['location_data']['meta_keywords']); ?>
" />
<link rel='canonical' href='<?php echo $this->_tpl_vars['config']['current_location']; ?>
<?php $mo=preg_replace("/&page=\d*/","",$_SERVER["REQUEST_URI"]); echo($mo); ?>' />
<?php if ($this->_tpl_vars['addons']['seo']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/seo/hooks/index/meta.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
