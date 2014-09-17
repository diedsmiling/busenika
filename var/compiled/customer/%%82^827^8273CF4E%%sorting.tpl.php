<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:45
         compiled from views/products/components/sorting.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_query_remove', 'views/products/components/sorting.tpl', 8, false),array('modifier', 'fn_get_products_sorting', 'views/products/components/sorting.tpl', 9, false),array('modifier', 'fn_get_products_views', 'views/products/components/sorting.tpl', 10, false),array('modifier', 'default', 'views/products/components/sorting.tpl', 11, false),array('modifier', 'count', 'views/products/components/sorting.tpl', 29, false),array('modifier', 'fn_url', 'views/products/components/sorting.tpl', 36, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('view_as','sort_by'));
?>

<!--dynamic:product_sorting-->
<?php if ($this->_tpl_vars['settings']['DHTML']['customer_ajax_based_pagination'] == 'Y'): ?>
	<?php $this->assign('ajax_class', "cm-ajax cm-ajax-force", false); ?>
<?php endif; ?>

<?php $this->assign('curl', fn_query_remove($this->_tpl_vars['config']['current_url'], 'sort_by', 'sort_order', 'result_ids', 'layout'), false); ?>
<?php $this->assign('sorting', fn_get_products_sorting("", 'false'), false); ?>
<?php $this->assign('layouts', fn_get_products_views("", false, false), false); ?>
<?php $this->assign('pagination_id', smarty_modifier_default(@$this->_tpl_vars['id'], 'pagination_contents'), false); ?>

<?php if ($this->_tpl_vars['search']['sort_order'] == 'asc'): ?>
	<?php ob_start(); ?>
		<a class="sort-asc"><?php echo $this->_tpl_vars['sorting'][$this->_tpl_vars['search']['sort_by']]['description']; ?>
</a>
	<?php $this->_smarty_vars['capture']['sorting_text'] = ob_get_contents(); ob_end_clean(); ?>
<?php else: ?>
	<?php ob_start(); ?>
		<a class="sort-desc"><?php echo $this->_tpl_vars['sorting'][$this->_tpl_vars['search']['sort_by']]['description']; ?>
</a>
	<?php $this->_smarty_vars['capture']['sorting_text'] = ob_get_contents(); ob_end_clean(); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['search']['sort_order'] == 'asc'): ?>
	<?php $this->assign('layout_sort_order', 'desc', false); ?>
<?php else: ?>
	<?php $this->assign('layout_sort_order', 'asc', false); ?>
<?php endif; ?>

<?php if (! ( ( count($this->_tpl_vars['category_data']['selected_layouts']) == 1 ) || ( count($this->_tpl_vars['category_data']['selected_layouts']) == 0 && count(fn_get_products_views("", true)) <= 1 ) ) && ! $this->_tpl_vars['hide_layouts']): ?>
<div class="float-left">
<strong><?php echo fn_get_lang_var('view_as', $this->getLanguage()); ?>
:</strong>&nbsp;
<?php ob_start(); ?>
	<ul>
	<?php $_from = $this->_tpl_vars['layouts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['layout'] => $this->_tpl_vars['item']):
?>
		<?php if (( $this->_tpl_vars['category_data']['selected_layouts'][$this->_tpl_vars['layout']] ) || ( ! $this->_tpl_vars['category_data']['selected_layouts'] && $this->_tpl_vars['item']['active'] )): ?>
			<li><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
 <?php if ($this->_tpl_vars['layout'] == $this->_tpl_vars['selected_layout']): ?>active<?php endif; ?>" rev="<?php echo $this->_tpl_vars['pagination_id']; ?>
" href="<?php echo fn_url(($this->_tpl_vars['curl'])."&amp;sort_by=".($this->_tpl_vars['search']['sort_by'])."&amp;sort_order=".($this->_tpl_vars['layout_sort_order'])."&amp;layout=".($this->_tpl_vars['layout'])); ?>
" rel="nofollow" name="layout_callback"><?php echo $this->_tpl_vars['item']['title']; ?>
</a></li>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	</ul>
<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tools_list' => $this->_smarty_vars['capture']['tools_list'],'suffix' => 'view_as','link_text' => $this->_tpl_vars['layouts'][$this->_tpl_vars['selected_layout']]['title'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endif; ?>

<div class="right">
	<a href="" class="no_paginate_link" style="float: left; margin-left: 117px; font-weight: bold;"> Показать все! </a>
<strong><?php echo fn_get_lang_var('sort_by', $this->getLanguage()); ?>
:</strong>&nbsp;
<?php ob_start(); ?>
	<ul>
		<?php $_from = $this->_tpl_vars['sorting']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['option'] => $this->_tpl_vars['value']):
?>
			<?php if ($this->_tpl_vars['search']['sort_by'] == $this->_tpl_vars['option']): ?>
				<?php $this->assign('sort_order', $this->_tpl_vars['search']['sort_order'], false); ?>
			<?php else: ?>
				<?php if ($this->_tpl_vars['value']['default_order']): ?>
					<?php $this->assign('sort_order', $this->_tpl_vars['value']['default_order'], false); ?>
				<?php else: ?>
					<?php $this->assign('sort_order', 'asc', false); ?>
				<?php endif; ?>
			<?php endif; ?>
			<li><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
 <?php if ($this->_tpl_vars['search']['sort_by'] == $this->_tpl_vars['option']): ?>active<?php endif; ?>" rev="<?php echo $this->_tpl_vars['pagination_id']; ?>
" href="<?php echo fn_url(($this->_tpl_vars['curl'])."&amp;sort_by=".($this->_tpl_vars['option'])."&amp;sort_order=".($this->_tpl_vars['sort_order'])); ?>
" rel="nofollow" name="sorting_callback"><?php echo $this->_tpl_vars['value']['description']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == $this->_tpl_vars['option']): ?>&nbsp;<?php if ($this->_tpl_vars['search']['sort_order'] == 'asc'): ?><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/sort_asc.gif" width="7" height="6" border="0" alt="" /><?php else: ?><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/sort_desc.gif" width="7" height="6" border="0" alt="" /><?php endif; ?><?php endif; ?></a>
			</li>
		<?php endforeach; endif; unset($_from); ?>
	</ul>
<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tools_list' => $this->_smarty_vars['capture']['tools_list'],'suffix' => 'sort_by','link_text' => $this->_smarty_vars['capture']['sorting_text'],'no_link' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<hr />
<!--/dynamic-->