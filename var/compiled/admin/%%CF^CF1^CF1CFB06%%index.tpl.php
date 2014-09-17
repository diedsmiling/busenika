<?php /* Smarty version 2.6.18, created on 2014-09-16 21:02:22
         compiled from views/index/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'views/index/index.tpl', 2, false),array('function', 'cycle', 'views/index/index.tpl', 48, false),array('block', 'hook', 'views/index/index.tpl', 5, false),array('modifier', 'fn_get_statuses', 'views/index/index.tpl', 12, false),array('modifier', 'lower', 'views/index/index.tpl', 20, false),array('modifier', 'fn_url', 'views/index/index.tpl', 23, false),array('modifier', 'date_format', 'views/index/index.tpl', 24, false),array('modifier', 'default', 'views/index/index.tpl', 65, false),array('modifier', 'defined', 'views/index/index.tpl', 127, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('latest_orders','order','by','for','no_items','orders_statistics','status','this_day','this_week','this_month','this_year','total_orders','gross_total','totally_paid','inventory','category_inventory','total','active','hidden','disabled','product_inventory','total','in_stock','active','disabled','downloadable','text_out_of_stock','hidden','free_shipping','users','customers','not_a_member','staff','root_administrators','total','disabled','shortcuts','manage_products','manage_categories','shipping_methods','payment_methods','general_settings','db_backup_restore','add_inf_page','manage_blocks','send_feedback','feedback_values','dashboard'));
?>
<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>

<?php ob_start(); ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "index:index")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table-fixed">
<tr valign="top">
<td width="64%">

 <div class="statistics-box orders">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader_statistic.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('latest_orders', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $this->assign('order_status_descr', fn_get_statuses(@STATUSES_ORDER, true, true, true), false); ?>
	<div class="statistics-body">
		<?php if ($this->_tpl_vars['latest_orders']): ?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<?php $_from = $this->_tpl_vars['latest_orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['order']):
?>
			<tr valign="top">
				<td width="15%">
				<?php $this->assign('status_descr', $this->_tpl_vars['order']['status'], false); ?>
				<span class="order-status order-<?php echo smarty_modifier_lower($this->_tpl_vars['order']['status']); ?>
"><em><?php echo $this->_tpl_vars['order_status_descr'][$this->_tpl_vars['status_descr']]; ?>
</em></span>
				</td>
				<td width="85%">
				<a href="<?php echo fn_url("orders.details?order_id=".($this->_tpl_vars['order']['order_id'])); ?>
"><?php echo fn_get_lang_var('order', $this->getLanguage()); ?>
&nbsp;#<?php echo $this->_tpl_vars['order']['order_id']; ?>
</a> <?php echo fn_get_lang_var('by', $this->getLanguage()); ?>
 <?php if ($this->_tpl_vars['order']['user_id']): ?><a href="<?php echo fn_url("profiles.update?user_id=".($this->_tpl_vars['order']['user_id'])); ?>
"><?php endif; ?><?php echo $this->_tpl_vars['order']['firstname']; ?>
 <?php echo $this->_tpl_vars['order']['lastname']; ?>
<?php if ($this->_tpl_vars['order']['user_id']): ?></a><?php endif; ?> <?php echo fn_get_lang_var('for', $this->getLanguage()); ?>
 <strong><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['order']['total'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strong>
				<p class="not-approved-text"><?php echo smarty_modifier_date_format($this->_tpl_vars['order']['timestamp'], ($this->_tpl_vars['settings']['Appearance']['date_format']).", ".($this->_tpl_vars['settings']['Appearance']['time_format'])); ?>
</p>
				</td>
			</tr>
			<?php endforeach; endif; unset($_from); ?>
		</table>
		<?php else: ?>
			<p class="no-items"><?php echo fn_get_lang_var('no_items', $this->getLanguage()); ?>
</p>
		<?php endif; ?>
	</div>
</div>
<!--
<div class="statistics-box statistic">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader_statistic.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('orders_statistics', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<div class="statistics-body">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
	<tr>
		<th><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</th>
		<th class="center"><?php echo fn_get_lang_var('this_day', $this->getLanguage()); ?>
</th>
		<th class="center"><?php echo fn_get_lang_var('this_week', $this->getLanguage()); ?>
</th>
		<th class="center"><?php echo fn_get_lang_var('this_month', $this->getLanguage()); ?>
</th>
		<th class="center"><?php echo fn_get_lang_var('this_year', $this->getLanguage()); ?>
</th>
	</tr>
	<?php $_from = $this->_tpl_vars['order_statuses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['_status'] => $this->_tpl_vars['status']):
?>
	<tr <?php echo smarty_function_cycle(array('values' => "class=\"table-row\", "), $this);?>
>
		<td><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/status.tpl", 'smarty_include_vars' => array('status' => $this->_tpl_vars['_status'],'display' => 'view')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['daily_orders'][$this->_tpl_vars['_status']]['amount']): ?><a href="<?php echo fn_url("orders.manage?status%5B%5D=".($this->_tpl_vars['_status'])."&amp;period=D"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['daily_orders'][$this->_tpl_vars['_status']]['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['weekly_orders'][$this->_tpl_vars['_status']]['amount']): ?><a href="<?php echo fn_url("orders.manage?status%5B%5D=".($this->_tpl_vars['_status'])."&amp;period=W"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['weekly_orders'][$this->_tpl_vars['_status']]['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['monthly_orders'][$this->_tpl_vars['_status']]['amount']): ?><a href="<?php echo fn_url("orders.manage?status%5B%5D=".($this->_tpl_vars['_status'])."&amp;period=M"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['monthly_orders'][$this->_tpl_vars['_status']]['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['year_orders'][$this->_tpl_vars['_status']]['amount']): ?><a href="<?php echo fn_url("orders.manage?status%5B%5D=".($this->_tpl_vars['_status'])."&amp;period=Y"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['year_orders'][$this->_tpl_vars['_status']]['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	<tr <?php echo smarty_function_cycle(array('values' => "class=\"table-row\", "), $this);?>
>
		<td><strong><?php echo fn_get_lang_var('total_orders', $this->getLanguage()); ?>
</strong></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['daily_orders']['totals']['amount']): ?><a href="<?php echo fn_url("orders.manage?period=D"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['daily_orders']['totals']['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['weekly_orders']['totals']['amount']): ?><a href="<?php echo fn_url("orders.manage?period=W"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['weekly_orders']['totals']['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['monthly_orders']['totals']['amount']): ?><a href="<?php echo fn_url("orders.manage?period=M"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['monthly_orders']['totals']['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
		<td class="center"><?php if ($this->_tpl_vars['orders_stats']['year_orders']['totals']['amount']): ?><a href="<?php echo fn_url("orders.manage?period=Y"); ?>
"><?php echo $this->_tpl_vars['orders_stats']['year_orders']['totals']['amount']; ?>
</a><?php else: ?>0<?php endif; ?></td>
	</tr>
	<tr class="strong">
		<td><?php echo fn_get_lang_var('gross_total', $this->getLanguage()); ?>
</td>
		<td class="center"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['daily_orders']['totals']['total'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		<td class="center"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['weekly_orders']['totals']['total'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		<td class="center"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['monthly_orders']['totals']['total'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		<td class="center"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['year_orders']['totals']['total'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
	</tr>
	<tr class="strong">
		<td><?php echo fn_get_lang_var('totally_paid', $this->getLanguage()); ?>
</td>
		<td class="center valued-text"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['daily_orders']['totals']['total_paid'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		<td class="center valued-text"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['weekly_orders']['totals']['total_paid'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		<td class="center valued-text"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['monthly_orders']['totals']['total_paid'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
		<td class="center valued-text"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => smarty_modifier_default(@$this->_tpl_vars['orders_stats']['year_orders']['totals']['total_paid'], '0'))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
	</tr>

	</table>
	</div>
</div>
-->
<?php $this->_tag_stack[] = array('hook', array('name' => "index:extra")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['addons']['discussion']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/hooks/index/extra.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

</td>

<td class="spacer">&nbsp;</td>

<td width="34%">
<div class="statistics-box inventory">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader_statistic.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('inventory', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<div class="statistics-body">
		<p class="strong"><?php echo fn_get_lang_var('category_inventory', $this->getLanguage()); ?>
:</p>
		<div class="clear">
			<ul class="float-left">
				<li><?php echo fn_get_lang_var('total', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['category_stats']['total']): ?><strong><?php echo $this->_tpl_vars['category_stats']['total']; ?>
</strong><?php else: ?>0<?php endif; ?></li>
				<li><?php echo fn_get_lang_var('active', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['category_stats']['status']['A']): ?><strong><?php echo $this->_tpl_vars['category_stats']['status']['A']; ?>
</strong><?php else: ?>0<?php endif; ?></li>
			</ul>
			<ul>
				<li><?php echo fn_get_lang_var('hidden', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['category_stats']['status']['H']): ?><strong><?php echo $this->_tpl_vars['category_stats']['status']['H']; ?>
</strong><?php else: ?>0<?php endif; ?></li>
				<li><?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['category_stats']['status']['D']): ?><strong><?php echo $this->_tpl_vars['category_stats']['status']['D']; ?>
</strong><?php else: ?>0<?php endif; ?></li>
			</ul>
		</div>
		
		<p class="strong"><?php echo fn_get_lang_var('product_inventory', $this->getLanguage()); ?>
:</p>
		<div class="clear">
			<ul class="float-left">
				<li><?php echo fn_get_lang_var('total', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['total']): ?><a href="<?php echo fn_url("products.manage"); ?>
"><?php echo $this->_tpl_vars['product_stats']['total']; ?>
</a><?php else: ?>0<?php endif; ?></li>
				<?php $this->_tag_stack[] = array('hook', array('name' => "index:inventory")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<?php if ($this->_tpl_vars['addons']['product_configurator']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/product_configurator/hooks/index/inventory.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
				<li><?php echo fn_get_lang_var('in_stock', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['in_stock']): ?><a href="<?php echo fn_url("products.manage?amount_from=1&amp;amount_to=&amp;tracking[]=B&amp;tracking[]=O"); ?>
"><?php echo $this->_tpl_vars['product_stats']['in_stock']; ?>
</a><?php else: ?>0<?php endif; ?></li>
				<li><?php echo fn_get_lang_var('active', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['status']['A']): ?><a href="<?php echo fn_url("products.manage?status=A"); ?>
"><?php echo $this->_tpl_vars['product_stats']['status']['A']; ?>
</a><?php else: ?>0<?php endif; ?></li>
				<li><?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['status']['D']): ?><a href="<?php echo fn_url("products.manage?status=D"); ?>
"><?php echo $this->_tpl_vars['product_stats']['status']['D']; ?>
</a><?php else: ?>0<?php endif; ?></li>
			</ul>
			<ul>
				<li><?php echo fn_get_lang_var('downloadable', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['downloadable']): ?><a href="<?php echo fn_url("products.manage?downloadable=Y"); ?>
"><?php echo $this->_tpl_vars['product_stats']['downloadable']; ?>
</a><?php else: ?>0<?php endif; ?></li>
				<li><?php echo fn_get_lang_var('text_out_of_stock', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['out_of_stock']): ?><a href="<?php echo fn_url("products.manage?amount_from=&amp;amount_to=0&amp;tracking[]=B&amp;tracking[]=O"); ?>
"><?php echo $this->_tpl_vars['product_stats']['out_of_stock']; ?>
</a><?php else: ?>0<?php endif; ?></li>
				<li><?php echo fn_get_lang_var('hidden', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['status']['H']): ?><a href="<?php echo fn_url("products.manage?status=H"); ?>
"><?php echo $this->_tpl_vars['product_stats']['status']['H']; ?>
</a><?php else: ?>0<?php endif; ?></li>

				<li><?php echo fn_get_lang_var('free_shipping', $this->getLanguage()); ?>
:&nbsp;<?php if ($this->_tpl_vars['product_stats']['free_shipping']): ?><a href="<?php echo fn_url("products.manage&amp;type=extended&amp;match=any&amp;free_shipping=Y"); ?>
"><?php echo $this->_tpl_vars['product_stats']['free_shipping']; ?>
</a><?php else: ?>0<?php endif; ?></li>
			</ul>
		</div>
	</div>
</div>

<?php if (! defined('COMPANY_ID')): ?>
<div class="statistics-box users">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader_statistic.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('users', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<div class="statistics-body clear">
	<ul>
		<li>
			<span><strong><?php echo fn_get_lang_var('customers', $this->getLanguage()); ?>
:</strong></span>
			<em><?php if ($this->_tpl_vars['users_stats']['total']['C']): ?><a href="<?php echo fn_url("profiles.manage?user_type=C"); ?>
"><?php echo $this->_tpl_vars['users_stats']['total']['C']; ?>
</a><?php else: ?>0<?php endif; ?></em>
		</li>

		<?php if ($this->_tpl_vars['usergroups_type']['C']): ?>
		<li>
			<span><?php echo fn_get_lang_var('not_a_member', $this->getLanguage()); ?>
:</span>
			<em><?php if ($this->_tpl_vars['users_stats']['not_members']['C']): ?><a href="<?php echo fn_url("profiles.manage?usergroup_id=0&amp;user_type=C"); ?>
"><?php echo $this->_tpl_vars['users_stats']['not_members']['C']; ?>
</a><?php else: ?>0<?php endif; ?></em>
		</li>
		<?php endif; ?>
		<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['mem_id'] => $this->_tpl_vars['mem_name']):
?>
		<?php if ($this->_tpl_vars['mem_name']['type'] == 'C'): ?>
			<li>
				<span><?php echo $this->_tpl_vars['mem_name']['usergroup']; ?>
:</span>
				<em><?php if ($this->_tpl_vars['users_stats']['usergroup']['C'][$this->_tpl_vars['mem_id']]): ?><a href="<?php echo fn_url("profiles.manage?usergroup_id=".($this->_tpl_vars['mem_id'])); ?>
"><?php echo $this->_tpl_vars['users_stats']['usergroup']['C'][$this->_tpl_vars['mem_id']]; ?>
</a><?php else: ?>0<?php endif; ?></em>
			</li>
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		<li>
			<span><strong><?php echo fn_get_lang_var('staff', $this->getLanguage()); ?>
:</strong></span>
			<em><?php if ($this->_tpl_vars['users_stats']['total']['A']): ?><a href="<?php echo fn_url("profiles.manage?user_type=A"); ?>
"><?php echo $this->_tpl_vars['users_stats']['total']['A']; ?>
</a><?php else: ?>0<?php endif; ?></em>
		</li>

		<?php if ($this->_tpl_vars['usergroups_type']['A']): ?>
		<li>
			<span><?php echo fn_get_lang_var('root_administrators', $this->getLanguage()); ?>
:</span>
			<em><?php if ($this->_tpl_vars['users_stats']['not_members']['A']): ?><a href="<?php echo fn_url("profiles.manage?usergroup_id=0&amp;user_type=A"); ?>
"><?php echo $this->_tpl_vars['users_stats']['not_members']['A']; ?>
</a><?php else: ?>0<?php endif; ?></em>
		</li>
		<?php endif; ?>
		<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['mem_id'] => $this->_tpl_vars['mem_name']):
?>
		<?php if ($this->_tpl_vars['mem_name']['type'] == 'A'): ?>
			<li>
				<span><?php echo $this->_tpl_vars['mem_name']['usergroup']; ?>
:</span>
				<em><?php if ($this->_tpl_vars['users_stats']['usergroup']['A'][$this->_tpl_vars['mem_id']]): ?><a href="<?php echo fn_url("profiles.manage?usergroup_id=".($this->_tpl_vars['mem_id'])); ?>
"><?php echo $this->_tpl_vars['users_stats']['usergroup']['A'][$this->_tpl_vars['mem_id']]; ?>
</a><?php else: ?>0<?php endif; ?></em>
			</li>
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		<?php $this->_tag_stack[] = array('hook', array('name' => "index:users")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php if ($this->_tpl_vars['addons']['affiliate']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/affiliate/hooks/index/users.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		
		<li><hr /></li>
		
		<li>
			<span><strong><?php echo fn_get_lang_var('total', $this->getLanguage()); ?>
:</strong></span>
			<em><?php if ($this->_tpl_vars['users_stats']['total_all']): ?><a href="<?php echo fn_url("profiles.manage"); ?>
"><?php echo $this->_tpl_vars['users_stats']['total_all']; ?>
</a><?php else: ?>0<?php endif; ?></em>
		</li>

		<li>
			<span><?php echo fn_get_lang_var('disabled', $this->getLanguage()); ?>
:</span>
			<em><?php if ($this->_tpl_vars['users_stats']['not_approved']): ?><a href="<?php echo fn_url("profiles.manage?status=D"); ?>
"><?php echo $this->_tpl_vars['users_stats']['not_approved']; ?>
</a><?php else: ?>0<?php endif; ?></em>
		</li>
	</ul>
	</div>
</div>
<div class="statistics-box">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader_statistic.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('shortcuts', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<div class="statistics-body clear">
		<ul class="arrow-list float-left">
			<li><a href="<?php echo fn_url("products.manage"); ?>
"><?php echo fn_get_lang_var('manage_products', $this->getLanguage()); ?>
</a></li>
			<li><a href="<?php echo fn_url("categories.manage"); ?>
"><?php echo fn_get_lang_var('manage_categories', $this->getLanguage()); ?>
</a></li>
			<li><a href="<?php echo fn_url("shippings.manage"); ?>
"><?php echo fn_get_lang_var('shipping_methods', $this->getLanguage()); ?>
</a></li>
			<li><a href="<?php echo fn_url("payments.manage"); ?>
"><?php echo fn_get_lang_var('payment_methods', $this->getLanguage()); ?>
</a></li>
		</ul>

		<ul class="arrow-list float-left">
			<li><a href="<?php echo fn_url("settings.manage"); ?>
"><?php echo fn_get_lang_var('general_settings', $this->getLanguage()); ?>
</a></li>
			<li><a href="<?php echo fn_url("database.manage"); ?>
"><?php echo fn_get_lang_var('db_backup_restore', $this->getLanguage()); ?>
</a></li>
			<li><a href="<?php echo fn_url("pages.add&amp;parent_id=0"); ?>
"><?php echo fn_get_lang_var('add_inf_page', $this->getLanguage()); ?>
</a></li>
			<li><a href="<?php echo fn_url("block_manager.manage"); ?>
"><?php echo fn_get_lang_var('manage_blocks', $this->getLanguage()); ?>
</a></li>
		</ul>
	</div>
</div>
<?php endif; ?>

</td>
</tr>
</table>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['settings']['General']['feedback_type'] == 'manual' && ! defined('COMPANY_ID')): ?>
		<div class="tools-container">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/object_group.tpl", 'smarty_include_vars' => array('link_text' => (fn_get_lang_var('send_feedback', $this->getLanguage()))."&nbsp;&#155;&#155;",'content' => $this->_smarty_vars['capture']['update_block'],'id' => 'feedback','no_table' => true,'header_text' => fn_get_lang_var('feedback_values', $this->getLanguage()),'but_name' => "dispatch[feedback.send]",'href' => "feedback.prepare",'opener_ajax_class' => "cm-ajax",'link_class' => "cm-ajax-force",'picker_meta' => "cm-clear-content",'act' => 'edit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']['tools'] = ob_get_contents(); ob_end_clean(); ?>
<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('dashboard', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'],'tools' => $this->_smarty_vars['capture']['tools'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>