<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:16
         compiled from common_templates/attach_images.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'common_templates/attach_images.tpl', 12, false),array('modifier', 'define', 'common_templates/attach_images.tpl', 13, false),array('modifier', 'explode', 'common_templates/attach_images.tpl', 36, false),array('modifier', 'default', 'common_templates/attach_images.tpl', 37, false),array('modifier', 'fn_url', 'common_templates/attach_images.tpl', 56, false),array('function', 'script', 'common_templates/attach_images.tpl', 119, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('text_thumbnail_manual_loading','thumbnail_manual_loading_link','delete_image_pair','thumbnail','delete_image','alt_text','popup_larger_image','delete_image','alt_text'));
?>


<?php if (! defined('SMARTY_ATTACH_IMAGES_LOADED')): ?>
<?php $this->assign('tmp', define('SMARTY_ATTACH_IMAGES_LOADED', true), false); ?>
<script type="text/javascript">
	//<![CDATA[
	<?php echo '
	function fn_delete_image(r, p)
	{
		if (r.deleted == true) {
			$(\'#\' + p.result_ids).replaceWith(\'<img border="0" src="\' + images_dir + \'/no_image.gif" />\');
			$(\'a[rev=\' + p.result_ids + \']\').hide();
		}
	}
	
	function fn_delete_image_pair(r, p)
	{
		if (r.deleted == true) {
			$(\'#\' + p.result_ids).remove();
		}
	}
	'; ?>

	//]]>
</script>
<?php endif; ?>

<?php $this->assign('_plug', explode(".", ""), false); ?>
<?php $this->assign('key', smarty_modifier_default(@$this->_tpl_vars['image_key'], '0'), false); ?>
<?php $this->assign('object_id', smarty_modifier_default(@$this->_tpl_vars['image_object_id'], '0'), false); ?>
<?php $this->assign('name', smarty_modifier_default(@$this->_tpl_vars['image_name'], ""), false); ?>
<?php $this->assign('object_type', smarty_modifier_default(@$this->_tpl_vars['image_object_type'], ""), false); ?>
<?php $this->assign('type', smarty_modifier_default(@$this->_tpl_vars['image_type'], 'M'), false); ?>
<?php $this->assign('pair', smarty_modifier_default(@$this->_tpl_vars['image_pair'], @$this->_tpl_vars['_plug']), false); ?>
<?php $this->assign('suffix', smarty_modifier_default(@$this->_tpl_vars['image_suffix'], ""), false); ?>

<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
_image_data<?php echo $this->_tpl_vars['suffix']; ?>
[<?php echo $this->_tpl_vars['key']; ?>
][pair_id]" value="<?php echo $this->_tpl_vars['pair']['pair_id']; ?>
" class="cm-image-field" />
<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
_image_data<?php echo $this->_tpl_vars['suffix']; ?>
[<?php echo $this->_tpl_vars['key']; ?>
][type]" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['type'], 'M'); ?>
" class="cm-image-field" />
<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
_image_data<?php echo $this->_tpl_vars['suffix']; ?>
[<?php echo $this->_tpl_vars['key']; ?>
][object_id]" value="<?php echo $this->_tpl_vars['object_id']; ?>
" class="cm-image-field" />

<div id="box_attach_images_<?php echo $this->_tpl_vars['name']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
">
	<?php if ($this->_tpl_vars['no_thumbnail'] && ! $this->_tpl_vars['pair']['icon']): ?>
		<?php echo fn_get_lang_var('text_thumbnail_manual_loading', $this->getLanguage()); ?>
&nbsp;<a id="sw_load_thumbnail_<?php echo $this->_tpl_vars['name']; ?>
<?php echo $this->_tpl_vars['suffix']; ?>
<?php echo $this->_tpl_vars['key']; ?>
" class="cm-combination dashed"><?php echo fn_get_lang_var('thumbnail_manual_loading_link', $this->getLanguage()); ?>
</a>
	<?php endif; ?>
	<div class="clear <?php if ($this->_tpl_vars['no_thumbnail'] && ! $this->_tpl_vars['pair']['icon']): ?>hidden<?php endif; ?>" id="load_thumbnail_<?php echo $this->_tpl_vars['name']; ?>
<?php echo $this->_tpl_vars['suffix']; ?>
<?php echo $this->_tpl_vars['key']; ?>
">
	<?php if ($this->_tpl_vars['delete_pair'] && $this->_tpl_vars['pair']['pair_id']): ?>
		<div class="float-right">
			<a rev="box_attach_images_<?php echo $this->_tpl_vars['name']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
" href="<?php echo fn_url("image.delete_image_pair?pair_id=".($this->_tpl_vars['pair']['pair_id'])."&amp;object_type=".($this->_tpl_vars['object_type'])); ?>
" class="cm-confirm cm-ajax delete" name="delete_image_pair"><?php echo fn_get_lang_var('delete_image_pair', $this->getLanguage()); ?>
</a>
		</div>
	<?php endif; ?>
		<?php if (! $this->_tpl_vars['hide_titles']): ?>
			<p>
				<span class="field-name"><?php echo smarty_modifier_default(@$this->_tpl_vars['icon_title'], fn_get_lang_var('thumbnail', $this->getLanguage())); ?>
</span>
				<?php if ($this->_tpl_vars['icon_text']): ?><span class="small-note"><?php echo $this->_tpl_vars['icon_text']; ?>
</span><?php endif; ?>
				<span class="field-name">:</span>
			</p>
		<?php endif; ?>
		
		<?php if (! $this->_tpl_vars['hide_images']): ?>
			<div class="float-left image">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('image' => $this->_tpl_vars['pair']['icon'],'image_id' => $this->_tpl_vars['pair']['image_id'],'image_width' => 85,'object_type' => $this->_tpl_vars['object_type'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php if ($this->_tpl_vars['pair']['image_id']): ?>
				<p>
					<a rev="image_<?php echo $this->_tpl_vars['object_type']; ?>
_<?php echo $this->_tpl_vars['pair']['image_id']; ?>
" href="<?php echo fn_url("image.delete_image?pair_id=".($this->_tpl_vars['pair']['pair_id'])."&amp;image_id=".($this->_tpl_vars['pair']['image_id'])."&amp;object_type=".($this->_tpl_vars['object_type'])); ?>
" class="cm-confirm cm-ajax delete" name="delete_image"><?php echo fn_get_lang_var('delete_image', $this->getLanguage()); ?>
</a>
				</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<div class="float-left attach-images-alt">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/fileuploader.tpl", 'smarty_include_vars' => array('var_name' => ($this->_tpl_vars['name'])."_image_icon".($this->_tpl_vars['suffix'])."[".($this->_tpl_vars['key'])."]",'image' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php if (! $this->_tpl_vars['hide_alt']): ?>
			<label for="alt_icon_<?php echo $this->_tpl_vars['name']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
"><?php echo fn_get_lang_var('alt_text', $this->getLanguage()); ?>
:</label>
			<input type="text" class="input-text cm-image-field" id="alt_icon_<?php echo $this->_tpl_vars['name']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
" name="<?php echo $this->_tpl_vars['name']; ?>
_image_data<?php echo $this->_tpl_vars['suffix']; ?>
[<?php echo $this->_tpl_vars['key']; ?>
][image_alt]" value="<?php echo $this->_tpl_vars['pair']['icon']['alt']; ?>
" />
			<?php endif; ?>
		</div>
	</div>
	
	<?php if (! $this->_tpl_vars['no_detailed']): ?>
	<div class="clear margin-top">
		<?php if (! $this->_tpl_vars['hide_titles']): ?>
			<p>
				<span class="field-name"><?php echo smarty_modifier_default(@$this->_tpl_vars['detailed_title'], fn_get_lang_var('popup_larger_image', $this->getLanguage())); ?>
</span>
				<?php if ($this->_tpl_vars['detailed_text']): ?>
					<span class="small-note"><?php echo $this->_tpl_vars['detailed_text']; ?>
</span>
				<?php endif; ?>
				<span class="field-name">:</span>
			</p>
		<?php endif; ?>
		
		<?php if (! $this->_tpl_vars['hide_images']): ?>
			<div class="float-left image">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('image' => $this->_tpl_vars['pair']['detailed'],'image_id' => $this->_tpl_vars['pair']['detailed_id'],'image_width' => 85,'object_type' => 'detailed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php if ($this->_tpl_vars['pair']['detailed_id']): ?>
				<p>
					<a rev="image_detailed_<?php echo $this->_tpl_vars['pair']['detailed_id']; ?>
" href="<?php echo fn_url("image.delete_image?pair_id=".($this->_tpl_vars['pair']['pair_id'])."&amp;image_id=".($this->_tpl_vars['pair']['detailed_id'])."&amp;object_type=detailed"); ?>
" class="cm-confirm cm-ajax delete" name="delete_image"><?php echo fn_get_lang_var('delete_image', $this->getLanguage()); ?>
</a>
				</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		
		<div class="float-left attach-images-alt">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/fileuploader.tpl", 'smarty_include_vars' => array('var_name' => ($this->_tpl_vars['name'])."_image_detailed".($this->_tpl_vars['suffix'])."[".($this->_tpl_vars['key'])."]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php if (! $this->_tpl_vars['hide_alt']): ?>
			<label for="alt_det_<?php echo $this->_tpl_vars['name']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
"><?php echo fn_get_lang_var('alt_text', $this->getLanguage()); ?>
:</label>
			<input type="text" class="input-text cm-image-field" id="alt_det_<?php echo $this->_tpl_vars['name']; ?>
_<?php echo $this->_tpl_vars['key']; ?>
" name="<?php echo $this->_tpl_vars['name']; ?>
_image_data<?php echo $this->_tpl_vars['suffix']; ?>
[<?php echo $this->_tpl_vars['key']; ?>
][detailed_alt]" value="<?php echo $this->_tpl_vars['pair']['detailed']['alt']; ?>
" />
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
</div><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>