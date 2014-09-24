<?php /* Smarty version 2.6.18, created on 2014-09-23 21:20:58
         compiled from views/categories/components/categories_tree.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'views/categories/components/categories_tree.tpl', 3, false),array('modifier', 'fn_url', 'views/categories/components/categories_tree.tpl', 11, false),array('function', 'math', 'views/categories/components/categories_tree.tpl', 18, false),)), $this); ?>
<?php  ob_start();  ?>
<?php $this->assign('cc_id', smarty_modifier_default(@$this->_tpl_vars['_REQUEST']['category_id'], @$_SESSION['current_category_id']), false); ?>
<?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['categories'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['categories']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['cat_key'] => $this->_tpl_vars['category']):
        $this->_foreach['categories']['iteration']++;
?>
	<?php if ($this->_tpl_vars['category']['level'] == '0'): ?>
		<?php if ($this->_tpl_vars['ul_subcategories'] == 'started'): ?>
			</ul>
			<?php $this->assign('ul_subcategories', "", false); ?>
		<?php endif; ?>
		<ul class="menu-root-categories">
			<li><a href="<?php echo fn_url("categories.view?category_id=".($this->_tpl_vars['category']['category_id'])); ?>
"><?php echo $this->_tpl_vars['category']['category']; ?>
</a></li>
		</ul>
	<?php else: ?>
		<?php if ($this->_tpl_vars['ul_subcategories'] != 'started'): ?>
			<ul class="menu-subcategories">
				<?php $this->assign('ul_subcategories', 'started', false); ?>
		<?php endif; ?>
		<li style="padding-left: <?php if ($this->_tpl_vars['category']['level'] == '1'): ?>13px<?php elseif ($this->_tpl_vars['category']['level'] > '1'): ?><?php echo smarty_function_math(array('equation' => "x*y+13",'x' => '7','y' => $this->_tpl_vars['category']['level']), $this);?>
px<?php endif; ?>;"><a href="<?php echo fn_url("categories.view?category_id=".($this->_tpl_vars['category']['category_id'])); ?>
"<?php if ($this->_tpl_vars['category']['category_id'] == $this->_tpl_vars['cc_id']): ?> class="active"<?php endif; ?>><?php echo $this->_tpl_vars['category']['category']; ?>
</a></li>
	<?php endif; ?>
	<?php if (($this->_foreach['categories']['iteration'] == $this->_foreach['categories']['total']) && $this->_tpl_vars['ul_subcategories'] == 'started'): ?>
		</ul>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php  ob_end_flush();  ?>