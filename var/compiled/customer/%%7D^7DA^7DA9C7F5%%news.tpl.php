<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:59
         compiled from addons/news_and_emails/blocks/news.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'addons/news_and_emails/blocks/news.tpl', 8, false),array('modifier', 'fn_url', 'addons/news_and_emails/blocks/news.tpl', 9, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('view_all'));
?>
<?php  ob_start();  ?>
<?php if ($this->_tpl_vars['items']): ?>
<ul class="site-news">
<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['site_news'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['site_news']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['news']):
        $this->_foreach['site_news']['iteration']++;
?>
	<li>
		<strong><?php echo smarty_modifier_date_format($this->_tpl_vars['news']['date'], $this->_tpl_vars['settings']['Appearance']['date_format']); ?>
</strong>
		<a href="<?php echo fn_url("news.view?news_id=".($this->_tpl_vars['news']['news_id'])."#".($this->_tpl_vars['news']['news_id'])); ?>
"><?php echo $this->_tpl_vars['news']['news']; ?>
</a>
	</li>
	<?php if (! ($this->_foreach['site_news']['iteration'] == $this->_foreach['site_news']['total'])): ?>
	<li class="delim"></li>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
</ul>

<p class="right">
	<a href="<?php echo fn_url("news.list"); ?>
" class="extra-link"><?php echo fn_get_lang_var('view_all', $this->getLanguage()); ?>
</a>
</p>
<?php endif; ?>
<?php  ob_end_flush();  ?>