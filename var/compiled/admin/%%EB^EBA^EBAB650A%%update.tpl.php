<?php /* Smarty version 2.6.18, created on 2014-09-15 23:39:45
         compiled from addons/buy_together/views/buy_together/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'addons/buy_together/views/buy_together/update.tpl', 18, false),array('modifier', 'default', 'addons/buy_together/views/buy_together/update.tpl', 47, false),array('function', 'script', 'addons/buy_together/views/buy_together/update.tpl', 102, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('general','products','name','description','avail_from','avail_till','display_in_promotions','combination_products','recalculate','total_cost','price_for_all','share_discount','apply'));
?>

<?php if ($this->_tpl_vars['item']['chain_id']): ?>
	<?php $this->assign('mode', 'update', false); ?>
<?php else: ?>
	<?php $this->assign('mode', 'add', false); ?>
	<?php $this->assign('extra_mode', 'buy_together', false); ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['item']['product_id']): ?>
	<?php $this->assign('product_id', $this->_tpl_vars['item']['product_id'], false); ?>
<?php else: ?>
	<?php $this->assign('product_id', $this->_tpl_vars['product_id'], false); ?>
<?php endif; ?>

<div id="content_group_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
">

<form action="<?php echo fn_url(""); ?>
" method="post" name="item_update_form_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" class="cm-form-highlight" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="item_id" value="<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" />
<input type="hidden" name="product_id" value="<?php echo $this->_tpl_vars['product_id']; ?>
" />

<div class="object-container">
	<div class="tabs cm-j-tabs">
		<ul>
			<li id="tab_general_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" class="cm-js cm-active"><a><?php echo fn_get_lang_var('general', $this->getLanguage()); ?>
</a></li>
			<li id="tab_products_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" class="cm-js"><a><?php echo fn_get_lang_var('products', $this->getLanguage()); ?>
</a></li>
		</ul>
	</div>

	<div class="cm-tabs-content" id="tabs_content_<?php echo $this->_tpl_vars['id']; ?>
">
		<fieldset>
			<div id="content_tab_general_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
">
				<div class="form-field">
					<label for="item_name_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" class="cm-required"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
					<input type="text" name="item_data[name]" id="item_name_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" size="55" value="<?php echo $this->_tpl_vars['item']['name']; ?>
" class="input-text-large main-input" />
				</div>
				
				<div class="form-field">
					<label for="item_description<?php echo $this->_tpl_vars['item']['chain_id']; ?>
"><?php echo fn_get_lang_var('description', $this->getLanguage()); ?>
:</label>
					<textarea id="item_description<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" name="item_data[description]" cols="55" rows="8" class="input-textarea-long"><?php echo $this->_tpl_vars['item']['description']; ?>
</textarea>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/wysiwyg.tpl", 'smarty_include_vars' => array('id' => "item_description".($this->_tpl_vars['item']['chain_id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
				
				<div class="form-field">
					<label for="item_date_from_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
"><?php echo fn_get_lang_var('avail_from', $this->getLanguage()); ?>
:</label>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => "item_date_from_bt_".($this->_tpl_vars['item']['chain_id']),'date_name' => "item_data[date_from]",'date_val' => smarty_modifier_default(@$this->_tpl_vars['item']['date_from'], @TIME),'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
				
				<div class="form-field">
					<label for="item_date_to_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
"><?php echo fn_get_lang_var('avail_till', $this->getLanguage()); ?>
:</label>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/calendar.tpl", 'smarty_include_vars' => array('date_id' => "item_date_to_bt_".($this->_tpl_vars['item']['chain_id']),'date_name' => "item_data[date_to]",'date_val' => smarty_modifier_default(@$this->_tpl_vars['item']['date_to'], @TIME),'start_year' => $this->_tpl_vars['settings']['Company']['company_start_year'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
				
				<div class="form-field">
					<label for="item_display_in_promotions_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
"><?php echo fn_get_lang_var('display_in_promotions', $this->getLanguage()); ?>
:</label>
					<input type="hidden" name="item_data[display_in_promotions]" value="N" />
					<input type="checkbox" name="item_data[display_in_promotions]" id="item_display_in_promotions_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" value="Y" <?php if ($this->_tpl_vars['item']['display_in_promotions'] == 'Y'): ?>checked="checked"<?php endif; ?> class="checkbox" />
				</div>
				
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_status.tpl", 'smarty_include_vars' => array('input_name' => "item_data[status]",'id' => "item_status_bt_".($this->_tpl_vars['item']['chain_id']),'obj' => $this->_tpl_vars['item'],'hidden' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>
			
			<div id="content_tab_products_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('combination_products', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/products_picker.tpl", 'smarty_include_vars' => array('data_id' => "objects_".($this->_tpl_vars['item']['chain_id'])."_",'input_name' => "item_data[products]",'item_ids' => $this->_tpl_vars['item']['products_info'],'type' => 'table','aoc' => true,'colspan' => '7')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				
				<ul class="statistic-list">
					<li>
						<strong><a onclick="fn_buy_together_recalculate('<?php echo $this->_tpl_vars['item']['chain_id']; ?>
');"><?php echo fn_get_lang_var('recalculate', $this->getLanguage()); ?>
</a></strong>
					</li>
					<li>
						<em><?php echo fn_get_lang_var('total_cost', $this->getLanguage()); ?>
:</em>
						<strong><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['item']['total_price'],'span_id' => "total_price_".($this->_tpl_vars['item']['chain_id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strong>
					</li>
					<li>
						<em><?php echo fn_get_lang_var('price_for_all', $this->getLanguage()); ?>
:</em>
						<strong><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['item']['chain_price'],'span_id' => "price_for_all_".($this->_tpl_vars['item']['chain_id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strong>
					</li>
					<li>
						<em><label for="global_discount_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
"><?php echo fn_get_lang_var('share_discount', $this->getLanguage()); ?>
</label>&nbsp;(<?php echo $this->_tpl_vars['currencies'][$this->_tpl_vars['primary_currency']]['symbol']; ?>
):</em>
						<input type="text" class="input-text" size="4" id="global_discount_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
" onkeypress="fn_buy_together_share_discount(event, '<?php echo $this->_tpl_vars['item']['chain_id']; ?>
');" />&nbsp;<a onclick="fn_buy_together_apply_discount('<?php echo $this->_tpl_vars['item']['chain_id']; ?>
');"><?php echo fn_get_lang_var('apply', $this->getLanguage()); ?>
</a>
					</li>
				</ul>
			</div>
		</fieldset>
	</div>
</div>

<div class="buttons-container">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[buy_together.update]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

</form>

<script type="text/javascript">
	var customer_index = '<?php echo $this->_tpl_vars['config']['customer_index']; ?>
';
	//fn_buy_together_recalculate('<?php echo $this->_tpl_vars['item']['chain_id']; ?>
', '<?php echo $this->_tpl_vars['product_id']; ?>
');
</script>

<!--content_group_bt_<?php echo $this->_tpl_vars['item']['chain_id']; ?>
--></div><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>