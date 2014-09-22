<?php /* Smarty version 2.6.18, created on 2014-09-22 23:33:09
         compiled from blocks/product_filters_extended.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_text_placeholders', 'blocks/product_filters_extended.tpl', 14, false),array('modifier', 'fn_url', 'blocks/product_filters_extended.tpl', 16, false),array('modifier', 'fn_add_range_to_url_hash', 'blocks/product_filters_extended.tpl', 16, false),)), $this); ?>
<?php  ob_start();  ?>
<!--dynamic:filters_extended-->
<?php if ($this->_tpl_vars['items']): ?>

<?php $this->assign('fh', $this->_tpl_vars['_REQUEST']['features_hash'], false); ?>
<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['filters'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['filters']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['filter']):
        $this->_foreach['filters']['iteration']++;
?>
<ul class="product-filters" id="content_product_more_filters_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['filter']['filter_id']; ?>
">
<?php $_from = $this->_tpl_vars['filter']['ranges']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['ranges'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ranges']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['range']):
        $this->_foreach['ranges']['iteration']++;
?>
	<li>
		<?php echo ''; ?><?php if ($this->_tpl_vars['range']['selected'] == true): ?><?php echo ''; ?><?php echo fn_text_placeholders($this->_tpl_vars['range']['range_name']); ?><?php echo ''; ?><?php else: ?><?php echo '<a href="'; ?><?php if ($this->_tpl_vars['filter']['feature_type'] == 'E' && ! $this->_tpl_vars['filter']['simple_link']): ?><?php echo ''; ?><?php echo fn_url("product_features.view?variant_id=".($this->_tpl_vars['range']['range_id'])); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php $this->assign('filter_features_hash', fn_add_range_to_url_hash("", $this->_tpl_vars['range'], $this->_tpl_vars['filter']['field_type']), false); ?><?php echo ''; ?><?php echo fn_url("products.search?features_hash=".($this->_tpl_vars['filter_features_hash'])."&amp;variant_id=".($this->_tpl_vars['range']['range_id'])); ?><?php echo ''; ?><?php endif; ?><?php echo '">'; ?><?php echo fn_text_placeholders($this->_tpl_vars['range']['range_name']); ?><?php echo '</a>'; ?><?php endif; ?><?php echo ''; ?>

	</li>
<?php endforeach; endif; unset($_from); ?>
</ul>
<?php endforeach; endif; unset($_from); ?>

<?php endif; ?>
<!--/dynamic--><?php  ob_end_flush();  ?>