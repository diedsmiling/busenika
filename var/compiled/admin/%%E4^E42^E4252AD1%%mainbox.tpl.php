<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:41
         compiled from common_templates/mainbox.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'common_templates/mainbox.tpl', 13, false),array('modifier', 'default', 'common_templates/mainbox.tpl', 24, false),array('modifier', 'sizeof', 'common_templates/mainbox.tpl', 40, false),array('modifier', 'fn_link_attach', 'common_templates/mainbox.tpl', 42, false),array('modifier', 'trim', 'common_templates/mainbox.tpl', 46, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('back_to'));
?>
<?php if ($this->_tpl_vars['anchor']): ?>
<a name="<?php echo $this->_tpl_vars['anchor']; ?>
"></a>
<?php endif; ?>
<div>

<?php if ($this->_tpl_vars['title_extra'] || $this->_tpl_vars['tools'] || ( $this->_tpl_vars['navigation']['dynamic'] && $this->_tpl_vars['navigation']['dynamic']['actions'] ) || $this->_tpl_vars['select_languages'] || $this->_tpl_vars['extra_tools']): ?>
	<div class="clear mainbox-title-container">
<?php endif; ?>

	<?php if ($this->_tpl_vars['breadcrumbs']): ?>
	<div>
	<?php $_from = $this->_tpl_vars['breadcrumbs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['f_b'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['f_b']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['b']):
        $this->_foreach['f_b']['iteration']++;
?><a class="back-link strong" href="<?php echo fn_url($this->_tpl_vars['b']['link']); ?>
"><?php if (($this->_foreach['f_b']['iteration'] <= 1)): ?>&laquo; <?php echo fn_get_lang_var('back_to', $this->getLanguage()); ?>
:&nbsp;<?php endif; ?><?php echo $this->_tpl_vars['b']['title']; ?>
</a><?php if (! ($this->_foreach['f_b']['iteration'] == $this->_foreach['f_b']['total'])): ?>&nbsp;::&nbsp;<?php endif; ?><?php endforeach; endif; unset($_from); ?>
	</div>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['notes']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/help.tpl", 'smarty_include_vars' => array('content' => $this->_tpl_vars['notes'],'id' => $this->_tpl_vars['notes_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['tools']): ?><?php echo $this->_tpl_vars['tools']; ?>
<?php endif; ?>

	<h1 class="mainbox-title<?php if ($this->_tpl_vars['title_extra']): ?> float-left<?php endif; ?>">
		<?php echo smarty_modifier_default(@$this->_tpl_vars['title'], "&nbsp;"); ?>

	</h1>

	<?php if (! $this->_tpl_vars['title_extra'] && ! $this->_tpl_vars['tools'] && ! $this->_tpl_vars['notes']): ?>
		<div class="mainbox-title-bg">&nbsp;</div>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['title_extra']): ?><div class="title">-&nbsp;</div>
		<?php echo $this->_tpl_vars['title_extra']; ?>

	<?php endif; ?>
<?php if ($this->_tpl_vars['title_extra'] || $this->_tpl_vars['tools'] || $this->_tpl_vars['navigation']['dynamic']['actions'] || $this->_tpl_vars['select_languages'] || $this->_tpl_vars['extra_tools']): ?>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['navigation']['dynamic']['actions'] || $this->_tpl_vars['select_languages'] || $this->_tpl_vars['extra_tools']): ?><div class="extra-tools"><?php endif; ?>

<?php if ($this->_tpl_vars['select_languages'] && sizeof($this->_tpl_vars['languages']) > 1): ?>
<div class="select-lang">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_object.tpl", 'smarty_include_vars' => array('style' => 'graphic','link_tpl' => fn_link_attach($this->_tpl_vars['config']['current_url'], "descr_sl="),'items' => $this->_tpl_vars['languages'],'selected_id' => @DESCR_SL,'key_name' => 'name','suffix' => 'content','display_icons' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div><?php if ($this->_tpl_vars['navigation']['dynamic']['actions'] || $this->_tpl_vars['extra_tools']): ?>&nbsp;|&nbsp;<?php endif; ?>
<?php endif; ?>

<?php if (trim($this->_tpl_vars['extra_tools'])): ?>
	<?php echo $this->_tpl_vars['extra_tools']; ?>
<?php if ($this->_tpl_vars['navigation']['dynamic']['actions']): ?>&nbsp;|&nbsp;<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['navigation']['dynamic']['actions']): ?>
	<?php $_from = $this->_tpl_vars['navigation']['dynamic']['actions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['actions'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['actions']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['title'] => $this->_tpl_vars['m']):
        $this->_foreach['actions']['iteration']++;
?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_href' => $this->_tpl_vars['m']['href'],'but_text' => fn_get_lang_var($this->_tpl_vars['title'], $this->getLanguage()),'but_role' => 'tool','but_target' => $this->_tpl_vars['m']['target'],'but_meta' => $this->_tpl_vars['m']['meta'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php if (! ($this->_foreach['actions']['iteration'] == $this->_foreach['actions']['total'])): ?>&nbsp;|&nbsp;<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['navigation']['dynamic']['actions'] || $this->_tpl_vars['select_languages'] || $this->_tpl_vars['extra_tools']): ?></div><?php endif; ?>

	<div class="mainbox-body">
		<?php echo smarty_modifier_default(@$this->_tpl_vars['content'], "&nbsp;"); ?>

	</div>
</div>