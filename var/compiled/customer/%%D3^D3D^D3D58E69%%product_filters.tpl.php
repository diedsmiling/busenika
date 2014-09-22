<?php /* Smarty version 2.6.18, created on 2014-09-22 22:43:52
         compiled from blocks/product_filters.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strpos', 'blocks/product_filters.tpl', 7, false),array('modifier', 'fn_query_remove', 'blocks/product_filters.tpl', 9, false),array('modifier', 'fn_link_attach', 'blocks/product_filters.tpl', 17, false),array('modifier', 'fn_delete_range_from_url', 'blocks/product_filters.tpl', 30, false),array('modifier', 'fn_url', 'blocks/product_filters.tpl', 40, false),array('modifier', 'fn_text_placeholders', 'blocks/product_filters.tpl', 40, false),array('modifier', 'fn_add_range_to_url_hash', 'blocks/product_filters.tpl', 46, false),array('modifier', 'unescape', 'blocks/product_filters.tpl', 74, false),array('modifier', 'escape', 'blocks/product_filters.tpl', 76, false),array('modifier', 'defined', 'blocks/product_filters.tpl', 88, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('remove','remove','choose_other','more','view_all','advanced','reset'));
?>
<?php  ob_start();  ?>
<!--dynamic:filters-->
<?php if ($this->_tpl_vars['items'] && ! $this->_tpl_vars['_REQUEST']['advanced_filter']): ?>

<?php if (strpos($_SERVER['QUERY_STRING'], "dispatch=") !== false): ?>
	<?php $this->assign('curl', $this->_tpl_vars['config']['current_url'], false); ?>
	<?php $this->assign('filter_qstring', fn_query_remove($this->_tpl_vars['curl'], 'result_ids', 'filter_id', 'view_all', 'req_range_id', 'advanced_filter', 'features_hash', 'subcats', 'page'), false); ?>
<?php else: ?>
	<?php $this->assign('filter_qstring', "products.search", false); ?>
<?php endif; ?>

<?php $this->assign('reset_qstring', "products.search", false); ?>

<?php if ($this->_tpl_vars['_REQUEST']['category_id']): ?>
	<?php $this->assign('filter_qstring', fn_link_attach($this->_tpl_vars['filter_qstring'], "subcats=Y"), false); ?>
	<?php $this->assign('reset_qstring', fn_link_attach($this->_tpl_vars['reset_qstring'], "subcats=Y"), false); ?>
<?php endif; ?>

<?php $this->assign('has_selected', false, false); ?>
<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['filters'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['filters']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['filter']):
        $this->_foreach['filters']['iteration']++;
?>

<h4><?php echo $this->_tpl_vars['filter']['filter']; ?>
</h4>
<ul class="product-filters" id="content_product_more_filters_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['filter']['filter_id']; ?>
">
<?php $_from = $this->_tpl_vars['filter']['ranges']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['ranges'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ranges']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['range']):
        $this->_foreach['ranges']['iteration']++;
?>
	<li <?php if ($this->_foreach['ranges']['iteration'] > @FILTERS_RANGES_COUNT): ?>class="hidden"<?php endif; ?>>
		<?php echo ''; ?><?php if ($this->_tpl_vars['range']['selected'] == true): ?><?php echo ''; ?><?php $this->assign('fh', fn_delete_range_from_url($this->_tpl_vars['_REQUEST']['features_hash'], $this->_tpl_vars['range'], $this->_tpl_vars['filter']['field_type']), false); ?><?php echo ''; ?><?php if ($this->_tpl_vars['fh']): ?><?php echo ''; ?><?php $this->assign('attach_query', "features_hash=".($this->_tpl_vars['fh']), false); ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['filter']['feature_type'] == 'E' && $this->_tpl_vars['range']['range_id'] == $this->_tpl_vars['_REQUEST']['variant_id']): ?><?php echo ''; ?><?php $this->assign('reset_lnk', $this->_tpl_vars['reset_qstring'], false); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php $this->assign('reset_lnk', $this->_tpl_vars['filter_qstring'], false); ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php $this->assign('has_selected', true, false); ?><?php echo '<a class="extra-link filter-delete" href="'; ?><?php if ($this->_tpl_vars['fh']): ?><?php echo ''; ?><?php echo fn_url(fn_link_attach($this->_tpl_vars['reset_lnk'], $this->_tpl_vars['attach_query'])); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php echo fn_url($this->_tpl_vars['reset_lnk']); ?><?php echo ''; ?><?php endif; ?><?php echo '" title="'; ?><?php echo fn_get_lang_var('remove', $this->getLanguage()); ?><?php echo '"><img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/icons/delete_icon.gif" width="12" height="11" border="0" alt="'; ?><?php echo fn_get_lang_var('remove', $this->getLanguage()); ?><?php echo '" align="bottom" /></a>'; ?><?php echo $this->_tpl_vars['filter']['prefix']; ?><?php echo ''; ?><?php echo fn_text_placeholders($this->_tpl_vars['range']['range_name']); ?><?php echo ''; ?><?php echo $this->_tpl_vars['filter']['suffix']; ?><?php echo ''; ?><?php if ($this->_tpl_vars['filter']['other_variants']): ?><?php echo '<ul id="other_variants_'; ?><?php echo $this->_tpl_vars['block']['block_id']; ?><?php echo '_'; ?><?php echo $this->_tpl_vars['filter']['filter_id']; ?><?php echo '" class="hidden">'; ?><?php $_from = $this->_tpl_vars['filter']['other_variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['r']):
?><?php echo '<li>'; ?><?php $this->assign('filter_query_elm', fn_add_range_to_url_hash($this->_tpl_vars['fh'], $this->_tpl_vars['r'], $this->_tpl_vars['filter']['field_type']), false); ?><?php echo ''; ?><?php if ($this->_tpl_vars['fh']): ?><?php echo ''; ?><?php $this->assign('cur_features_hash', "&amp;features_hash=".($this->_tpl_vars['fh']), false); ?><?php echo ''; ?><?php endif; ?><?php echo '<a href="'; ?><?php if ($this->_tpl_vars['r']['feature_type'] == 'E' && ! $this->_tpl_vars['r']['simple_link'] && $this->_tpl_vars['controller'] == 'product_features'): ?><?php echo ''; ?><?php echo fn_url("product_features.view?variant_id=".($this->_tpl_vars['r']['range_id']).($this->_tpl_vars['cur_features_hash'])); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php echo fn_url(fn_link_attach($this->_tpl_vars['filter_qstring'], "features_hash=".($this->_tpl_vars['filter_query_elm']))); ?><?php echo ''; ?><?php endif; ?><?php echo '">'; ?><?php echo $this->_tpl_vars['filter']['prefix']; ?><?php echo ''; ?><?php echo fn_text_placeholders($this->_tpl_vars['r']['range_name']); ?><?php echo ''; ?><?php echo $this->_tpl_vars['filter']['suffix']; ?><?php echo '</a>&nbsp;<span class="details">&nbsp;('; ?><?php echo $this->_tpl_vars['r']['products']; ?><?php echo ')</span></li>'; ?><?php endforeach; endif; unset($_from); ?><?php echo '</ul><p><a id="sw_other_variants_'; ?><?php echo $this->_tpl_vars['block']['block_id']; ?><?php echo '_'; ?><?php echo $this->_tpl_vars['filter']['filter_id']; ?><?php echo '" class="extra-link cm-combination">'; ?><?php echo fn_get_lang_var('choose_other', $this->getLanguage()); ?><?php echo '</a></p>'; ?><?php endif; ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php $this->assign('filter_query_elm', fn_add_range_to_url_hash($this->_tpl_vars['_REQUEST']['features_hash'], $this->_tpl_vars['range'], $this->_tpl_vars['filter']['field_type']), false); ?><?php echo ''; ?><?php if ($this->_tpl_vars['_REQUEST']['features_hash']): ?><?php echo ''; ?><?php $this->assign('cur_features_hash', "&amp;features_hash=".($this->_tpl_vars['_REQUEST']['features_hash']), false); ?><?php echo ''; ?><?php endif; ?><?php echo '<a href="'; ?><?php if ($this->_tpl_vars['filter']['feature_type'] == 'E' && ! $this->_tpl_vars['filter']['simple_link']): ?><?php echo ''; ?><?php echo fn_url("product_features.view?variant_id=".($this->_tpl_vars['range']['range_id']).($this->_tpl_vars['cur_features_hash'])); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php echo fn_url(fn_link_attach($this->_tpl_vars['filter_qstring'], "features_hash=".($this->_tpl_vars['filter_query_elm']))); ?><?php echo ''; ?><?php endif; ?><?php echo '">'; ?><?php echo $this->_tpl_vars['filter']['prefix']; ?><?php echo ''; ?><?php echo fn_text_placeholders($this->_tpl_vars['range']['range_name']); ?><?php echo ''; ?><?php echo $this->_tpl_vars['filter']['suffix']; ?><?php echo '</a>&nbsp;<span class="details">&nbsp;('; ?><?php echo $this->_tpl_vars['range']['products']; ?><?php echo ')</span>'; ?><?php endif; ?><?php echo ''; ?>

	</li>
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_foreach['ranges']['iteration'] > @FILTERS_RANGES_COUNT): ?>
	<li class="right">
		<a href="<?php echo fn_url(($this->_tpl_vars['filter_qstring'])."?filter_id=".($this->_tpl_vars['filter']['filter_id'])."&amp;more_filters=Y"); ?>
" onclick="$('#content_product_more_filters_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['filter']['filter_id']; ?>
 li').show(); $('#view_all_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['filter']['filter_id']; ?>
').show(); $(this).hide(); return false;" class="extra-link"><?php echo fn_get_lang_var('more', $this->getLanguage()); ?>
</a>
	</li>
<?php endif; ?>

<?php if ($this->_tpl_vars['filter']['more_cut']): ?>
	<?php ob_start(); ?><?php echo smarty_modifier_unescape($this->_tpl_vars['filter_qstring']); ?>
&filter_id=<?php echo $this->_tpl_vars['filter']['filter_id']; ?>
&<?php if ($this->_tpl_vars['_REQUEST']['features_hash']): ?>&features_hash=<?php echo fn_delete_range_from_url($this->_tpl_vars['_REQUEST']['features_hash'], $this->_tpl_vars['range'], $this->_tpl_vars['filter']['field_type']); ?>
<?php endif; ?><?php $this->_smarty_vars['capture']['q'] = ob_get_contents(); ob_end_clean(); ?>
	<li id="view_all_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['filter']['filter_id']; ?>
" class="right hidden">
		<?php $this->assign('capture_q', smarty_modifier_escape($this->_smarty_vars['capture']['q'], 'url'), false); ?>
		<a href="<?php echo fn_url("product_features.view_all?q=".($this->_tpl_vars['capture_q'])); ?>
" class="extra-link"><?php echo fn_get_lang_var('view_all', $this->getLanguage()); ?>
</a>
	</li>
<?php endif; ?>

<li class="delim">&nbsp;</li>

</ul>

<?php endforeach; endif; unset($_from); ?>

<div class="clear filters-tools">
	<div class="float-right"><a <?php if (defined('FILTER_CUSTOM_ADVANCED')): ?>href="<?php echo fn_url("products.search?advanced_filter=Y"); ?>
"<?php else: ?>href="<?php echo fn_url(fn_link_attach($this->_tpl_vars['filter_qstring'], "advanced_filter=Y")); ?>
"<?php endif; ?> class="secondary-link lowercase"><?php echo fn_get_lang_var('advanced', $this->getLanguage()); ?>
</a></div>
	<?php if ($this->_tpl_vars['has_selected']): ?>
	<a href="<?php if ($this->_tpl_vars['_REQUEST']['category_id']): ?><?php echo fn_url("categories.view?category_id=".($this->_tpl_vars['_REQUEST']['category_id'])); ?>
<?php else: ?><?php echo fn_url($this->_tpl_vars['index_script']); ?>
<?php endif; ?>" class="reset-filters"><?php echo fn_get_lang_var('reset', $this->getLanguage()); ?>
</a>
	<?php endif; ?>
</div>
<?php endif; ?>
<!--/dynamic--><?php  ob_end_flush();  ?>