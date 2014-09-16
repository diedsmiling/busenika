<?php /* Smarty version 2.6.18, created on 2014-09-15 23:41:30
         compiled from views/categories/components/categories_tree_simple.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'views/categories/components/categories_tree_simple.tpl', 27, false),array('modifier', 'default', 'views/categories/components/categories_tree_simple.tpl', 32, false),array('modifier', 'fn_url', 'views/categories/components/categories_tree_simple.tpl', 58, false),array('function', 'cycle', 'views/categories/components/categories_tree_simple.tpl', 33, false),array('function', 'math', 'views/categories/components/categories_tree_simple.tpl', 34, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('check_uncheck_all','expand_collapse_list','expand_collapse_list','expand_collapse_list','expand_collapse_list','categories','products','expand_sublist_of_items','expand_sublist_of_items','expand_sublist_of_items','expand_sublist_of_items','collapse_sublist_of_items','collapse_sublist_of_items','disabled'));
?>
<?php if ($this->_tpl_vars['parent_id']): ?>
<div class="hidden" id="cat_<?php echo $this->_tpl_vars['parent_id']; ?>
">
<?php endif; ?>
<?php $_from = $this->_tpl_vars['categories_tree']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cur_cat']):
?>
<?php $this->assign('cat_id', $this->_tpl_vars['cur_cat']['category_id'], false); ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<?php if ($this->_tpl_vars['header'] && ! $this->_tpl_vars['parent_id']): ?>
<?php $this->assign('header', "", false); ?>
<tr>
	<th class="center" width="20">
	<?php if ($this->_tpl_vars['display'] != 'radio'): ?>
		<input type="checkbox" name="check_all" value="Y" title="<?php echo fn_get_lang_var('check_uncheck_all', $this->getLanguage()); ?>
" class="checkbox cm-check-items" />
	<?php endif; ?>
	</th>
	<th width="97%">
		<?php if ($this->_tpl_vars['show_all']): ?>
		<div class="float-left">
			<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus_minus.gif" width="13" height="12" border="0" id="on_cat" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combinations <?php if ($this->_tpl_vars['expand_all']): ?>hidden<?php endif; ?>"  />
			<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus_plus.gif" width="13" height="12" border="0" id="off_cat" alt="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_collapse_list', $this->getLanguage()); ?>
" class="hand cm-combinations <?php if (! $this->_tpl_vars['expand_all']): ?>hidden<?php endif; ?>" />
		</div>
		<?php endif; ?>
		&nbsp;<?php echo fn_get_lang_var('categories', $this->getLanguage()); ?>

	</th>
	<?php if (! defined('COMPANY_ID')): ?>
	<th class="right"><?php echo fn_get_lang_var('products', $this->getLanguage()); ?>
</th>
	<?php endif; ?>
</tr>
<?php endif; ?>
<?php $this->assign('level', smarty_modifier_default(@$this->_tpl_vars['cur_cat']['level'], 0), false); ?>
<tr <?php if ($this->_tpl_vars['level'] != '0'): ?><?php echo smarty_function_cycle(array('values' => "class=\"table-row\", "), $this);?>
<?php else: ?><?php echo smarty_function_cycle(array('values' => "",'reset' => 1), $this);?>
class="manage-root-row"<?php endif; ?>>
   	<?php echo smarty_function_math(array('equation' => "x*14",'x' => $this->_tpl_vars['level'],'assign' => 'shift'), $this);?>

	<td class="center" width="20">
		<?php if ($this->_tpl_vars['display'] == 'radio'): ?>
		<input type="radio" name="<?php echo $this->_tpl_vars['checkbox_name']; ?>
" value="<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
" class="radio cm-item" />
		<?php else: ?>
		<input type="checkbox" name="<?php echo $this->_tpl_vars['checkbox_name']; ?>
[<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
]" value="<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
" class="checkbox cm-item" />
		<?php endif; ?>
	</td>
	<td width="97%">
		<?php if ($this->_tpl_vars['cur_cat']['subcategories']): ?>
			<?php echo smarty_function_math(array('equation' => "x+10",'x' => $this->_tpl_vars['shift'],'assign' => '_shift'), $this);?>

		<?php else: ?>
			<?php echo smarty_function_math(array('equation' => "x+21",'x' => $this->_tpl_vars['shift'],'assign' => '_shift'), $this);?>

		<?php endif; ?>
		<table cellpadding="0" cellspacing="0" width="100%"	border="0">
		<tr>
			<td class="nowrap" style="padding-left: <?php echo $this->_tpl_vars['_shift']; ?>
px;">
				<?php if ($this->_tpl_vars['cur_cat']['has_children'] || $this->_tpl_vars['cur_cat']['subcategories']): ?>
					<?php if ($this->_tpl_vars['show_all']): ?>
					<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" alt="<?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?>
" id="on_cat_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
" class="hand cm-combination <?php if (isset ( $this->_tpl_vars['path'][$this->_tpl_vars['cat_id']] ) || $this->_tpl_vars['expand_all']): ?>hidden<?php endif; ?>" />
					<?php else: ?>
					<?php if ($this->_tpl_vars['except_id']): ?>
						<?php $this->assign('_except_id', "&except_id=".($this->_tpl_vars['except_id']), false); ?>
					<?php endif; ?>
					<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/plus.gif" width="14" height="9" border="0" alt="<?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('expand_sublist_of_items', $this->getLanguage()); ?>
" id="on_cat_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
" class="hand cm-combination <?php if (( isset ( $this->_tpl_vars['path'][$this->_tpl_vars['cat_id']] ) )): ?>hidden<?php endif; ?>" onclick="if (!$('#category_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
').children().get(0)) jQuery.ajaxRequest('<?php echo fn_url("categories.picker?category_id=".($this->_tpl_vars['cur_cat']['category_id'])."&display=".($this->_tpl_vars['display'])."&checkbox_name=".($this->_tpl_vars['checkbox_name']).($this->_tpl_vars['_except_id']), 'A', 'rel', '&'); ?>
', <?php echo $this->_tpl_vars['ldelim']; ?>
result_ids: 'cat_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
'<?php echo $this->_tpl_vars['rdelim']; ?>
)" />
					<?php endif; ?>
					<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/minus.gif" width="14" height="9" border="0" alt="<?php echo fn_get_lang_var('collapse_sublist_of_items', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('collapse_sublist_of_items', $this->getLanguage()); ?>
" id="off_cat_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
" class="hand cm-combination <?php if (! isset ( $this->_tpl_vars['path'][$this->_tpl_vars['cat_id']] ) && ( ! $this->_tpl_vars['expand_all'] || ! $this->_tpl_vars['show_all'] )): ?>hidden<?php endif; ?>" />
				<?php else: ?>
					&nbsp;
				<?php endif; ?></td>
			<td width="100%">
				<strong id="category_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
"><?php echo $this->_tpl_vars['cur_cat']['category']; ?>
</strong><?php if ($this->_tpl_vars['cur_cat']['status'] == 'N'): ?>&nbsp;<span class="small-note">-&nbsp;[<?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?>
]</span><?php endif; ?>
			</td>
		</tr>
		</table>
	</td>
	<?php if (! defined('COMPANY_ID')): ?>
	<td class="right">
		<?php echo $this->_tpl_vars['cur_cat']['product_count']; ?>
&nbsp;&nbsp;&nbsp;
	</td>
	<?php endif; ?>
</tr>
</table>

<?php if ($this->_tpl_vars['cur_cat']['has_children'] || $this->_tpl_vars['cur_cat']['subcategories']): ?>
	<div<?php if (! $this->_tpl_vars['expand_all']): ?> class="hidden"<?php endif; ?> id="cat_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
">
	<?php if ($this->_tpl_vars['cur_cat']['subcategories']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/categories/components/categories_tree_simple.tpl", 'smarty_include_vars' => array('categories_tree' => $this->_tpl_vars['cur_cat']['subcategories'],'parent_id' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
	<!--cat_<?php echo $this->_tpl_vars['cur_cat']['category_id']; ?>
--></div>
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['parent_id']): ?><!--cat_<?php echo $this->_tpl_vars['parent_id']; ?>
--></div><?php endif; ?>