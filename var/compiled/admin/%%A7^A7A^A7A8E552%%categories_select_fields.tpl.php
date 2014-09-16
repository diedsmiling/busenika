<?php /* Smarty version 2.6.18, created on 2014-09-15 23:43:02
         compiled from views/categories/components/categories_select_fields.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'hook', 'views/categories/components/categories_select_fields.tpl', 75, false),array('function', 'script', 'views/categories/components/categories_select_fields.tpl', 93, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('status','meta_description','product_details_layout','name','meta_keywords','category_description','image_pair','title','created_date','position','usergroups','localization','select_all','unselect_all'));
?>

<input type="hidden" name="selected_fields[object]" value="category" />

<table cellspacing="0" cellpadding="5" border="0" width="100%">
<tr valign="top">
	<td>
		<ul>
			<li class="select-field">
				<input type="hidden" value="status" name="selected_fields[data][]" />
				<input type="checkbox" value="status" name="selected_fields[data][]" id="elm_status" checked="checked" disabled="disabled" class="checkbox cm-item-s" />
				<label for="elm_status"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="meta_description" name="selected_fields[data][]" id="elm_meta_description" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_meta_description"><?php echo fn_get_lang_var('meta_description', $this->getLanguage()); ?>
</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="product_details_layout" name="selected_fields[data][]" id="elm_product_details_layout" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_product_details_layout"><?php echo fn_get_lang_var('product_details_layout', $this->getLanguage()); ?>
</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="hidden" value="category" name="selected_fields[data][]" />
				<input type="checkbox" value="category" name="selected_fields[data][]" id="elm_category_name" checked="checked" disabled="disabled" class="checkbox cm-item-s" />
				<label for="elm_name"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="meta_keywords" name="selected_fields[data][]" id="elm_meta_keywords" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_meta_keywords"><?php echo fn_get_lang_var('meta_keywords', $this->getLanguage()); ?>
</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" value="description" name="selected_fields[data][]" id="elm_description" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_description"><?php echo fn_get_lang_var('category_description', $this->getLanguage()); ?>
</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="image_pair" name="selected_fields[images][]" id="elm_image_pair" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_image_pair"><?php echo fn_get_lang_var('image_pair', $this->getLanguage()); ?>
</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" value="page_title" id="elm_page_title" name="selected_fields[data][]" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_page_title"><?php echo fn_get_lang_var('title', $this->getLanguage()); ?>
</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="timestamp" id="elm_timestamp" name="selected_fields[data][]" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_timestamp"><?php echo fn_get_lang_var('created_date', $this->getLanguage()); ?>
</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" value="position" name="selected_fields[data][]" id="elm_position" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_position"><?php echo fn_get_lang_var('position', $this->getLanguage()); ?>
</label>
			</li>
			<li class="select-field">
				<input type="checkbox" value="usergroup_ids" name="selected_fields[data][]" id="elm_usergroup_ids" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_usergroup_ids"><?php echo fn_get_lang_var('usergroups', $this->getLanguage()); ?>
</label>
			</li>
		</ul>
	</td>
	<td>
		<ul>
			<?php $this->_tag_stack[] = array('hook', array('name' => "categories:fields_to_edit")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
			<?php if ($this->_tpl_vars['addons']['seo']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/seo/hooks/categories/fields_to_edit.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['discussion']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/hooks/categories/fields_to_edit.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		</ul>
	</td>
	<td>
		<ul>
			<li class="select-field">
				<input type="checkbox" id="elm_localization" value="localization" name="selected_fields[data][]" checked="checked" class="checkbox cm-item-s" />
				<label for="elm_localization"><?php echo fn_get_lang_var('localization', $this->getLanguage()); ?>
</label>
			</li>
		</ul>
	</td>
</tr>
</table>
<p>
<a name="check_all" class="cm-check-items-s cm-on underlined"><?php echo fn_get_lang_var('select_all', $this->getLanguage()); ?>
</a>&nbsp;/&nbsp;<a href="#sfields" name="check_all" class="cm-check-items-s cm-off underlined"><?php echo fn_get_lang_var('unselect_all', $this->getLanguage()); ?>
</a>
</p>

<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>