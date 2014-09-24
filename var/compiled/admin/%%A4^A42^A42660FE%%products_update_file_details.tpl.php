<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:18
         compiled from views/products/components/products_update_file_details.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/products/components/products_update_file_details.tpl', 3, false),array('modifier', 'fn_url', 'views/products/components/products_update_file_details.tpl', 5, false),array('modifier', 'md5', 'views/products/components/products_update_file_details.tpl', 31, false),array('modifier', 'formatfilesize', 'views/products/components/products_update_file_details.tpl', 33, false),array('modifier', 'number_format', 'views/products/components/products_update_file_details.tpl', 41, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('general','name','position','file','preview','bytes','none','activation_mode','manually','immediately','after_full_payment','max_downloads','license_agreement','agreement_required','yes','no','readme'));
?>

<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<form action="<?php echo fn_url(""); ?>
" method="post" class="cm-form-highlight" name="files_form_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
" enctype="multipart/form-data">
<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['product_id']; ?>
" />
<input type="hidden" name="selected_section" value="files" />
<input type="hidden" name="file_id" value="<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="tab_details_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
" class="cm-js cm-active"><a><?php echo fn_get_lang_var('general', $this->getLanguage()); ?>
</a></li>
		</ul>
	</div>
	
	<div class="cm-tabs-content" id="tabs_content_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
">
		<div id="content_tab_details_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
">

			<div class="form-field">
				<label for="name_<?php echo $this->_tpl_vars['product_file']['file']; ?>
" class="cm-required"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
				<input type="text" name="product_file[file_name]" id="name_<?php echo $this->_tpl_vars['product_file']['file']; ?>
" value="<?php echo $this->_tpl_vars['product_file']['file_name']; ?>
" class="input-text-large main-input" />
			</div>

			<div class="form-field">
				<label for="position_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
"><?php echo fn_get_lang_var('position', $this->getLanguage()); ?>
:</label>
				<input type="text" name="product_file[position]" id="position_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
" value="<?php echo $this->_tpl_vars['product_file']['position']; ?>
" size="3" class="input-text-short" />
			</div>

			<div class="form-field">
				<label for="type_<?php echo md5("base_file[".($this->_tpl_vars['product_file']['file_id'])."]"); ?>
" <?php if (! $this->_tpl_vars['product_file']): ?>class="cm-required"<?php endif; ?>><?php echo fn_get_lang_var('file', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['product_file']['file_path']): ?>
					<a href="<?php echo fn_url("products.getfile?file_id=".($this->_tpl_vars['product_file']['file_id'])); ?>
"><?php echo $this->_tpl_vars['product_file']['file_path']; ?>
</a> (<?php echo smarty_modifier_formatfilesize($this->_tpl_vars['product_file']['file_size']); ?>
)
				<?php endif; ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/fileuploader.tpl", 'smarty_include_vars' => array('var_name' => "base_file[".($this->_tpl_vars['product_file']['file_id'])."]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>

			<div class="form-field">
				<label for="type_<?php echo md5("file_preview[".($this->_tpl_vars['product_file']['file_id'])."]"); ?>
"><?php echo fn_get_lang_var('preview', $this->getLanguage()); ?>
:</label>
				<?php if ($this->_tpl_vars['product_file']['preview_path']): ?>
					<a href="<?php echo fn_url("products.getfile?file_id=".($this->_tpl_vars['product_file']['file_id'])."&amp;file_type=preview"); ?>
"><?php echo $this->_tpl_vars['product_file']['preview_path']; ?>
</a> (<?php echo number_format($this->_tpl_vars['product_file']['preview_size'], 0, "", ' '); ?>
&nbsp;<?php echo fn_get_lang_var('bytes', $this->getLanguage()); ?>
)
				<?php elseif ($this->_tpl_vars['product_file']): ?>
					<?php echo fn_get_lang_var('none', $this->getLanguage()); ?>

				<?php endif; ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/fileuploader.tpl", 'smarty_include_vars' => array('var_name' => "file_preview[".($this->_tpl_vars['product_file']['file_id'])."]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>

			<div class="form-field">
				<label for="activation_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
"><?php echo fn_get_lang_var('activation_mode', $this->getLanguage()); ?>
:</label>
				<select name="product_file[activation_type]" id="activation_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
">
					<option value="M" <?php if ($this->_tpl_vars['product_file']['activation_type'] == 'M'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('manually', $this->getLanguage()); ?>
</option>
					<option value="I" <?php if ($this->_tpl_vars['product_file']['activation_type'] == 'I'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('immediately', $this->getLanguage()); ?>
</option>
					<option value="P" <?php if ($this->_tpl_vars['product_file']['activation_type'] == 'P'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('after_full_payment', $this->getLanguage()); ?>
</option>
				</select>
			</div>

			<div class="form-field">
				<label for="max_downloads_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
"><?php echo fn_get_lang_var('max_downloads', $this->getLanguage()); ?>
:</label>
				<input type="text" name="product_file[max_downloads]" id="max_downloads_<?php echo $this->_tpl_vars['product_file']['file_id']; ?>
" value="<?php echo $this->_tpl_vars['product_file']['max_downloads']; ?>
" size="3" class="input-text-short" />
			</div>

			<div class="form-field">
				<label for="license_<?php echo $this->_tpl_vars['product_file']['file']; ?>
"><?php echo fn_get_lang_var('license_agreement', $this->getLanguage()); ?>
:</label>
				<textarea id="license_<?php echo $this->_tpl_vars['product_file']['file']; ?>
" name="product_file[license]" cols="55" rows="8" class="input-textarea-long"><?php echo $this->_tpl_vars['product_file']['license']; ?>
</textarea>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => "license_".($this->_tpl_vars['product_file']['file']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>

			<div class="form-field">
				<label><?php echo fn_get_lang_var('agreement_required', $this->getLanguage()); ?>
:</label>
				<div class="select-field float-left nowrap">
					<input type="radio" name="product_file[agreement]" id="agreement_<?php echo $this->_tpl_vars['product_file']['file']; ?>
_y" <?php if ($this->_tpl_vars['product_file']['agreement'] == 'Y' || ! $this->_tpl_vars['product_file']): ?>checked="checked"<?php endif; ?> value="Y" class="radio" />
					<label for="agreement_<?php echo $this->_tpl_vars['product_file']['file']; ?>
_y"><?php echo fn_get_lang_var('yes', $this->getLanguage()); ?>
</label>
					<input type="radio" name="product_file[agreement]" id="agreement_<?php echo $this->_tpl_vars['product_file']['file']; ?>
_n" <?php if ($this->_tpl_vars['product_file']['agreement'] == 'N'): ?>checked="checked"<?php endif; ?> value="N" class="radio" />
					<label for="agreement_<?php echo $this->_tpl_vars['product_file']['file']; ?>
_n"><?php echo fn_get_lang_var('no', $this->getLanguage()); ?>
</label>
				</div>
			</div>

			<div class="form-field">
				<label for="readme_<?php echo $this->_tpl_vars['product_file']['file']; ?>
"><?php echo fn_get_lang_var('readme', $this->getLanguage()); ?>
:</label>
				<textarea id="readme_<?php echo $this->_tpl_vars['product_file']['file']; ?>
" name="product_file[readme]" cols="55" rows="8" class="input-textarea-long"><?php echo $this->_tpl_vars['product_file']['readme']; ?>
</textarea>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => "readme_".($this->_tpl_vars['product_file']['file']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>
		</div>
	</div>
</div>

<div class="buttons-container">
	<?php if ($this->_tpl_vars['product_file']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[products.update_file]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('create' => true,'but_name' => "dispatch[products.update_file]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
</div>

</form>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>