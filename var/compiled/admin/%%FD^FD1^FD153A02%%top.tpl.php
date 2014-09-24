<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:11
         compiled from top.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'top.tpl', 5, false),array('modifier', 'trim', 'top.tpl', 11, false),array('modifier', 'sizeof', 'top.tpl', 13, false),array('modifier', 'fn_link_attach', 'top.tpl', 14, false),array('block', 'hook', 'top.tpl', 11, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('vendor'));
?>

<div id="header">
	<div id="logo">
		<a href="<?php echo fn_url($this->_tpl_vars['index_script']); ?>
"><img src="<?php if ($this->_tpl_vars['manifest']['Admin_logo']['vendor']): ?><?php echo $this->_tpl_vars['config']['images_path']; ?>
<?php else: ?><?php echo $this->_tpl_vars['images_dir']; ?>
/<?php endif; ?><?php echo $this->_tpl_vars['manifest']['Admin_logo']['filename']; ?>
" width="<?php echo $this->_tpl_vars['manifest']['Admin_logo']['width']; ?>
" height="<?php echo $this->_tpl_vars['manifest']['Admin_logo']['height']; ?>
" border="0" alt="<?php echo $this->_tpl_vars['manifest']['Admin_logo']['alt']; ?>
" title="<?php echo $this->_tpl_vars['manifest']['Admin_logo']['alt']; ?>
" /></a>
	</div>
	
	<div id="top_quick_links">
		<?php if ($this->_tpl_vars['auth']['user_id']): ?>

		<?php if ($this->_tpl_vars['addons']['statistics']['status'] == 'A'): ?><?php ob_start(); $this->_in_capture[] = '6ee9826ac104dc345862f295f2833e3f';
$_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/statistics/hooks/index/top.override.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->_tpl_vars['addon_content'] = ob_get_contents(); ob_end_clean(); array_pop($this->_in_capture); if (!empty($this->_scripts['6ee9826ac104dc345862f295f2833e3f'])) { echo implode("\n", $this->_scripts['6ee9826ac104dc345862f295f2833e3f']); unset($this->_scripts['6ee9826ac104dc345862f295f2833e3f']); }
 ?><?php else: ?><?php $this->assign('addon_content', "", false); ?><?php endif; ?><?php if (trim($this->_tpl_vars['addon_content'])): ?><?php echo $this->_tpl_vars['addon_content']; ?>
<?php else: ?><?php $this->_tag_stack[] = array('hook', array('name' => "index:top")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php endif; ?>
		<div>
			<?php if (sizeof($this->_tpl_vars['languages']) > 1): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_object.tpl", 'smarty_include_vars' => array('style' => 'graphic','link_tpl' => fn_link_attach($this->_tpl_vars['config']['current_url'], "sl="),'items' => $this->_tpl_vars['languages'],'selected_id' => @CART_LANGUAGE,'display_icons' => true,'key_name' => 'name','language_var_name' => 'sl','class' => 'languages')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
			<?php if (sizeof($this->_tpl_vars['languages']) > 1 && sizeof($this->_tpl_vars['currencies']) > 1): ?>&nbsp;|&nbsp;<?php endif; ?>
			<?php if (sizeof($this->_tpl_vars['currencies']) > 1): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_object.tpl", 'smarty_include_vars' => array('style' => 'graphic','link_tpl' => fn_link_attach($this->_tpl_vars['config']['current_url'], "currency="),'items' => $this->_tpl_vars['currencies'],'selected_id' => $this->_tpl_vars['secondary_currency'],'display_icons' => false,'key_name' => 'description')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
			<?php if (@PRODUCT_TYPE == 'MULTIVENDOR'): ?>
				<?php if (sizeof($this->_tpl_vars['s_companies']) > 1): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_object.tpl", 'smarty_include_vars' => array('style' => 'graphic','link_tpl' => fn_link_attach($this->_tpl_vars['config']['current_url'], "s_company="),'items' => $this->_tpl_vars['s_companies'],'selected_id' => $this->_tpl_vars['s_company'],'display_icons' => false,'key_name' => 'company')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php else: ?>
				<?php echo fn_get_lang_var('vendor', $this->getLanguage()); ?>
: <?php echo $this->_tpl_vars['s_companies'][$this->_tpl_vars['s_company']]['company']; ?>

				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="nowrap">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "top_quick_links.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<?php endif; ?>
	</div>
	
	<div id="menu_first_level">
		<?php if ($this->_tpl_vars['auth']['user_id']): ?>
		<ul id="menu_first_level_ul" class="clear">
			<li id="tabs_home" <?php if (! $this->_tpl_vars['navigation']['selected_tab']): ?>class="cm-active"<?php endif; ?>><a href="<?php echo fn_url($this->_tpl_vars['index_script']); ?>
">&nbsp;</a></li>
			<?php if ($this->_tpl_vars['navigation']['static']): ?>
			<?php $_from = $this->_tpl_vars['navigation']['static']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['title'] => $this->_tpl_vars['m']):
?>
			<li <?php if ($this->_tpl_vars['title'] == $this->_tpl_vars['navigation']['selected_tab']): ?>class="cm-active"<?php endif; ?> id="tabs_<?php echo $this->_tpl_vars['title']; ?>
"><a onclick="fn_switch_tab('<?php echo $this->_tpl_vars['title']; ?>
')"><?php echo fn_get_lang_var($this->_tpl_vars['title'], $this->getLanguage()); ?>
</a></li>
			<?php endforeach; endif; unset($_from); ?>
			<?php endif; ?>
		</ul>
		<?php endif; ?>
	</div>
	
	<div id="menu_second_level">
		<?php if ($this->_tpl_vars['auth']['user_id'] && $this->_tpl_vars['navigation']['static']): ?>
		<?php $_from = $this->_tpl_vars['navigation']['static']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['title'] => $this->_tpl_vars['m']):
?>
		<ul id="elements_<?php echo $this->_tpl_vars['title']; ?>
" class="clear<?php if ($this->_tpl_vars['title'] != $this->_tpl_vars['navigation']['selected_tab']): ?> hidden<?php endif; ?>">
			<?php $_from = $this->_tpl_vars['m']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['sec_level'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['sec_level']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['_title'] => $this->_tpl_vars['_m']):
        $this->_foreach['sec_level']['iteration']++;
?>
			<li class="<?php if ($this->_tpl_vars['_title'] == $this->_tpl_vars['navigation']['subsection'] && $this->_tpl_vars['title'] == $this->_tpl_vars['navigation']['selected_tab']): ?>cm-active<?php endif; ?> <?php if (($this->_foreach['sec_level']['iteration'] == $this->_foreach['sec_level']['total'])): ?>no-border<?php endif; ?>"><a href="<?php echo fn_url($this->_tpl_vars['_m']['href']); ?>
"><?php echo fn_get_lang_var($this->_tpl_vars['_title'], $this->getLanguage()); ?>
</a></li>
			<?php endforeach; endif; unset($_from); ?>
		</ul>
		<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
	</div>
<!--header--></div>

<?php echo '
<script type="text/javascript">
//<![CDATA[
function fn_switch_tab(section)
{
	$(\'#menu_second_level ul\').each(function(){
		var self = $(this);
		self.toggleBy(self.attr(\'id\') != \'elements_\' + section)
	});

	$(\'#menu_first_level_ul li\').each(function(){
		var self = $(this);
		if (self.attr(\'id\') != \'tabs_\' + section) {
			self.removeClass(\'cm-active\');
		} else {
			self.addClass(\'cm-active\');
		}
	});
}
//]]>
</script>
'; ?>
			