<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:13
         compiled from views/companies/components/product_company_data.tpl */ ?>
<?php
fn_preload_lang_vars(array('vendor','supplier'));
?>
<?php  ob_start();  ?><?php if (@PRODUCT_TYPE == 'MULTIVENDOR'): ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('vendor', $this->getLanguage()), false); ?>
<?php else: ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('supplier', $this->getLanguage()), false); ?>
<?php endif; ?>

		<?php if ($this->_tpl_vars['company_id'] && $this->_tpl_vars['settings']['Suppliers']['display_supplier'] == 'Y'): ?>
			<div class="form-field product-list-field">
				<label><?php echo $this->_tpl_vars['lang_vendor_supplier']; ?>
:</label>
				<?php echo $this->_tpl_vars['s_companies'][$this->_tpl_vars['company_id']]['company']; ?>

			</div>
		<?php endif; ?><?php  ob_end_flush();  ?>