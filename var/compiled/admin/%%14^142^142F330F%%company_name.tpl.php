<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from views/companies/components/company_name.tpl */ ?>
<?php
fn_preload_lang_vars(array('vendor','supplier'));
?>
<?php  ob_start();  ?>
<?php if (@PRODUCT_TYPE == 'MULTIVENDOR'): ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('vendor', $this->getLanguage()), false); ?>
<?php else: ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('supplier', $this->getLanguage()), false); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['company_id']): ?>
  (<?php echo $this->_tpl_vars['lang_vendor_supplier']; ?>
: <?php echo $this->_tpl_vars['s_companies'][$this->_tpl_vars['company_id']]['company']; ?>
)
<?php endif; ?>
<?php  ob_end_flush();  ?>