<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:12
         compiled from top_menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'top_menu.tpl', 9, false),)), $this); ?>

<?php if ($this->_tpl_vars['top_menu']): ?>
<div id="top_menu">
<?php echo '<ul class="top-menu dropdown">'; ?><?php $_from = $this->_tpl_vars['top_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?><?php echo '<li class="first-level '; ?><?php if ($this->_tpl_vars['m']['selected'] == true): ?><?php echo 'cm-active'; ?><?php endif; ?><?php echo '"><span><a'; ?><?php if ($this->_tpl_vars['m']['href']): ?><?php echo ' href="'; ?><?php echo fn_url($this->_tpl_vars['m']['href']); ?><?php echo '"'; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['m']['new_window']): ?><?php echo ' target="_blank"'; ?><?php endif; ?><?php echo '>'; ?><?php echo $this->_tpl_vars['m']['item']; ?><?php echo '</a></span>'; ?><?php if ($this->_tpl_vars['m']['subitems']): ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu.tpl", 'smarty_include_vars' => array('items' => $this->_tpl_vars['m']['subitems'],'top_menu' => "",'dir' => $this->_tpl_vars['m']['param_4'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo ''; ?><?php endif; ?><?php echo '</li>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '</ul>'; ?>

</div>
<span class="helper-block">&nbsp;</span>
<?php elseif ($this->_tpl_vars['items']): ?>
<ul <?php if ($this->_tpl_vars['dir'] == 'left'): ?>class="dropdown-vertical-rtl"<?php endif; ?>>
	<?php $this->assign('foreach_name', "cats_".($this->_tpl_vars['iter']), false); ?>
	<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach[$this->_tpl_vars['foreach_name']] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach[$this->_tpl_vars['foreach_name']]['total'] > 0):
    foreach ($_from as $this->_tpl_vars['_m']):
        $this->_foreach[$this->_tpl_vars['foreach_name']]['iteration']++;
?>
	<li <?php if ($this->_tpl_vars['_m']['subitems']): ?>class="dir"<?php endif; ?>>
		<a href="<?php echo fn_url($this->_tpl_vars['_m']['href']); ?>
"<?php if ($this->_tpl_vars['_m']['new_window']): ?> target="_blank"<?php endif; ?>><?php echo $this->_tpl_vars['_m']['item']; ?>
</a>
		<?php if ($this->_tpl_vars['_m']['subitems']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "top_menu.tpl", 'smarty_include_vars' => array('items' => $this->_tpl_vars['_m']['subitems'],'top_menu' => "",'dir' => $this->_tpl_vars['_m']['param_4'],'iter' => $this->_foreach[$this->_tpl_vars['foreach_name']]['iteration']+$this->_tpl_vars['iter'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
	</li>
	<?php if (! ($this->_foreach[$this->_tpl_vars['foreach_name']]['iteration'] == $this->_foreach[$this->_tpl_vars['foreach_name']]['total'])): ?>
	<li class="h-sep">&nbsp;</li>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
</ul>
<?php endif; ?>