<?php /* Smarty version 2.6.18, created on 2014-09-15 23:43:02
         compiled from views/categories/components/categories_tree.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'views/categories/components/categories_tree.tpl', 28, false),array('modifier', 'default', 'views/categories/components/categories_tree.tpl', 28, false),array('modifier', 'fn_url', 'views/categories/components/categories_tree.tpl', 40, false),array('modifier', 'defined', 'views/categories/components/categories_tree.tpl', 49, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('check_uncheck_all','position_short','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','name','products','status','expand_sublist_of_items','expand_sublist_of_items','expand_sublist_of_items','expand_sublist_of_items','collapse_sublist_of_items','collapse_sublist_of_items','disabled','manage_products','add'));
?>
<?php if ($this->_tpl_vars['parent_id']): ?>
<div class="hidden" id="cat_<?php echo $this->_tpl_vars['parent_id']; ?>
">
<?php endif; ?>
<?php $_from = $this->_tpl_vars['categories_tree']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['category']):
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-fixed">
<?php if ($this->_tpl_vars['header'] && ! $this->_tpl_vars['parent_id']): ?>
<?php $this->assign('header', "", false); ?>
<tr>
	<th class="center" width="3%">
		<input type="checkbox" name="check_all" value="Y" title="<?php echo fn_get_lang_var('check_uncheck_all', $this->getLanguage()); ?>
" class="checkbox cm-check-items" /></th>
	<th width="5%"><?php echo fn_get_lang_var('position_short', $this->getLanguage()); ?>
</th>
	<th width="62%">
		<?php if ($this->_tpl_vars['show_all']): ?>
		<div class="float-left">
			<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus_minus.gif" width="13" height="12" border="0" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" id="on_cat" class="hand cm-combinations<?php if ($this->_tpl_vars['expand_all']): ?> hidden<?php endif; ?>" />
			<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus_plus.gif" width="13" height="12" border="0" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" id="off_cat" class="hand cm-combinations<?php if (! $this->_tpl_vars['expand_all']): ?> hidden<?php endif; ?>" />
		</div>
		<?php endif; ?>
		&nbsp;<?php echo fn_get_lang_var('name', $this->getLanguage()); ?>

	</th>
	<th class="right" width="10%"><?php echo fn_get_lang_var('products', $this->getLanguage()); ?>
</th>
	<th width="10%"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</th>
	<th width="10%">&nbsp;</th>
</tr>
<?php endif; ?>
<tr <?php if ($this->_tpl_vars['category']['level'] > 0): ?>class="multiple-table-row"<?php endif; ?>>
   	<?php echo smarty_function_math(array('equation' => "x*14",'x' => smarty_modifier_default(@$this->_tpl_vars['category']['level'], '0'),'assign' => 'shift'), $this);?>

	<td class="center" width="3%">
		<input type="checkbox" name="category_ids[]" value="<?php echo $this->_tpl_vars['category']['category_id']; ?>
" class="checkbox cm-item" /></td>
	<td width="5%">
		<input type="text" name="categories_data[<?php echo $this->_tpl_vars['category']['category_id']; ?>
][position]" value="<?php echo $this->_tpl_vars['category']['position']; ?>
" size="3" class="input-text-short" /></td>
	<td width="62%">
	<?php echo '<span style="padding-left: '; ?><?php echo $this->_tpl_vars['shift']; ?><?php echo 'px;">'; ?><?php if ($this->_tpl_vars['category']['has_children'] || $this->_tpl_vars['category']['subcategories']): ?><?php echo ''; ?><?php if ($this->_tpl_vars['show_all']): ?><?php echo '<img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/plus.gif" width="14" height="9" border="0" alt="'; ?><?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?><?php echo '" title="'; ?><?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?><?php echo '" id="on_cat_'; ?><?php echo $this->_tpl_vars['category']['category_id']; ?><?php echo '" class="hand cm-combination '; ?><?php if ($this->_tpl_vars['expand_all']): ?><?php echo 'hidden'; ?><?php endif; ?><?php echo '" />'; ?><?php else: ?><?php echo '<img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/plus.gif" width="14" height="9" border="0" alt="'; ?><?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?><?php echo '" title="'; ?><?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?><?php echo '" id="on_cat_'; ?><?php echo $this->_tpl_vars['category']['category_id']; ?><?php echo '" class="hand cm-combination" onclick="if (!$(\'#cat_'; ?><?php echo $this->_tpl_vars['category']['category_id']; ?><?php echo '\').children().get(0)) jQuery.ajaxRequest(\''; ?><?php echo fn_url("categories.manage?category_id=".($this->_tpl_vars['category']['category_id']), 'A', 'rel', '&'); ?><?php echo '\', '; ?><?php echo $this->_tpl_vars['ldelim']; ?><?php echo 'result_ids: \'cat_'; ?><?php echo $this->_tpl_vars['category']['category_id']; ?><?php echo '\''; ?><?php echo $this->_tpl_vars['rdelim']; ?><?php echo ')" />'; ?><?php endif; ?><?php echo '<img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/minus.gif" width="14" height="9" border="0" alt="'; ?><?php echo fn_get_lang_var('collapse_sublist_of_items', $this->getLanguage()); ?><?php echo '" title="'; ?><?php echo fn_get_lang_var('collapse_sublist_of_items', $this->getLanguage()); ?><?php echo '" id="off_cat_'; ?><?php echo $this->_tpl_vars['category']['category_id']; ?><?php echo '" class="hand cm-combination'; ?><?php if (! $this->_tpl_vars['expand_all'] || ! $this->_tpl_vars['show_all']): ?><?php echo ' hidden'; ?><?php endif; ?><?php echo '" />&nbsp;'; ?><?php endif; ?><?php echo '<a href="'; ?><?php echo fn_url("categories.update?category_id=".($this->_tpl_vars['category']['category_id'])); ?><?php echo '"'; ?><?php if ($this->_tpl_vars['category']['status'] == 'N'): ?><?php echo ' class="manage-root-item-disabled"'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_tpl_vars['category']['subcategories']): ?><?php echo ' style="padding-left: 14px;"'; ?><?php endif; ?><?php echo ' >'; ?><?php echo $this->_tpl_vars['category']['category']; ?><?php echo '</a>'; ?><?php if ($this->_tpl_vars['category']['status'] == 'N'): ?><?php echo '&nbsp;<span class="small-note">-&nbsp;['; ?><?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?><?php echo ']</span>'; ?><?php endif; ?><?php echo '</span>'; ?>

	</td>
	<td width="10%" class="nowrap right">
		<a href="<?php echo fn_url("products.manage?cid=".($this->_tpl_vars['category']['category_id'])); ?>
"><?php if (defined('COMPANY_ID')): ?><?php echo fn_get_lang_var('manage_products', $this->getLanguage()); ?>
<?php else: ?><u>&nbsp;<?php echo $this->_tpl_vars['category']['product_count']; ?>
&nbsp;</u><?php endif; ?></a>&nbsp;
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('add', $this->getLanguage()),'but_href' => "products.add?category_id=".($this->_tpl_vars['category']['category_id']),'but_role' => 'add')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	<td width="10%">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_popup.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['category']['category_id'],'status' => $this->_tpl_vars['category']['status'],'hidden' => true,'object_id_name' => 'category_id','table' => 'categories')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	<td width="10%" class="nowrap">
		<center>
		<?php ob_start(); ?>
		<li><a class="cm-confirm" href="<?php echo fn_url("categories.delete?category_id=".($this->_tpl_vars['category']['category_id'])); ?>
"><img src="/images/cross.png"></a></li>
		<?php $this->_smarty_vars['capture']['tools_items'] = ob_get_contents(); ob_end_clean(); ?>
		<a href="zenon.php?dispatch=categories.update&category_id=<?php echo $this->_tpl_vars['category']['category_id']; ?>
"><img src="/images/edit.png"></a>
		<a onclick="return confirm('Are you sure you want to delete?')" href="zenon.php?dispatch=categories.delete&category_id=<?php echo $this->_tpl_vars['category']['category_id']; ?>
"><img src="/images/cross.png"></a>
		</center>
	</td>
</tr>
</table>
<?php if ($this->_tpl_vars['category']['has_children'] || $this->_tpl_vars['category']['subcategories']): ?>
	<div<?php if (! $this->_tpl_vars['expand_all']): ?> class="hidden"<?php endif; ?> id="cat_<?php echo $this->_tpl_vars['category']['category_id']; ?>
">
	<?php if ($this->_tpl_vars['category']['subcategories']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/categories/components/categories_tree.tpl", 'smarty_include_vars' => array('categories_tree' => $this->_tpl_vars['category']['subcategories'],'parent_id' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
	<!--cat_<?php echo $this->_tpl_vars['category']['category_id']; ?>
--></div>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['parent_id']): ?><!--cat_<?php echo $this->_tpl_vars['parent_id']; ?>
--></div><?php endif; ?>