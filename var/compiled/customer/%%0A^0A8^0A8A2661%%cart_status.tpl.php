<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:58
         compiled from views/checkout/components/cart_status.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/checkout/components/cart_status.tpl', 8, false),array('modifier', 'array_reverse', 'views/checkout/components/cart_status.tpl', 20, false),array('modifier', 'fn_get_product_name', 'views/checkout/components/cart_status.tpl', 24, false),array('modifier', 'escape', 'views/checkout/components/cart_status.tpl', 24, false),array('modifier', 'defined', 'views/checkout/components/cart_status.tpl', 24, false),array('block', 'hook', 'views/checkout/components/cart_status.tpl', 19, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('cart','cart','items','subtotal','cart_is_empty','cart_is_empty','cart_is_empty','cart','cart_is_empty','view_cart','checkout','checkout'));
?>

<div id="cart_status">
<!--dynamic:cart_status-->
<div class="float-left">
	<?php if ($_SESSION['cart']['amount']): ?>
		<img id="sw_cart_box" class="cm-combination cm-combo-on valign hand" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/filled_cart_icon.gif" border="0" alt="<?php echo fn_get_lang_var('cart', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('cart', $this->getLanguage()); ?>
" />
		<span class="lowercase"><a href="<?php echo fn_url("checkout.cart"); ?>
"><strong><?php echo $_SESSION['cart']['amount']; ?>
</strong>&nbsp;<?php echo fn_get_lang_var('items', $this->getLanguage()); ?>
</a>, 			<?php echo fn_get_lang_var('subtotal', $this->getLanguage()); ?>
:&nbsp;</span><strong><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $_SESSION['cart']['display_subtotal'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></strong>
	<?php else: ?>
		<img id="sw_cart_box" class="cm-combination cm-combo-on valign hand" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/empty_cart_icon.gif" alt="<?php echo fn_get_lang_var('cart_is_empty', $this->getLanguage()); ?>
" title="<?php echo fn_get_lang_var('cart_is_empty', $this->getLanguage()); ?>
" /><strong>&nbsp;&nbsp;&nbsp;<?php echo fn_get_lang_var('cart_is_empty', $this->getLanguage()); ?>
</strong>
	<?php endif; ?>
	
	<div id="cart_box" class="cart-list hidden cm-popup-box cm-smart-position">
		<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/<?php if ($_SESSION['cart']['amount']): ?>filled<?php else: ?>empty<?php endif; ?>_cart_list_icon.gif" alt="<?php echo fn_get_lang_var('cart', $this->getLanguage()); ?>
" class="cm-popup-switch hand cart-list-icon" />
		<div class="list-container">
			<div class="list">
			<?php if ($_SESSION['cart']['amount']): ?>
				<ul>
					<?php $this->_tag_stack[] = array('hook', array('name' => "index:cart_status")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
					<?php $this->assign('_cart_products', array_reverse($_SESSION['cart']['products'], true), false); ?>
					<?php $_from = $this->_tpl_vars['_cart_products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['cart_products'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['cart_products']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['p']):
        $this->_foreach['cart_products']['iteration']++;
?>
					<?php if (! $this->_tpl_vars['p']['extra']['parent']): ?>
					<li class="clear">
						<a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['p']['product_id'])); ?>
"><?php echo smarty_modifier_escape(fn_get_product_name($this->_tpl_vars['p']['product_id'])); ?>
</a><?php if (! defined('CHECKOUT') || $this->_tpl_vars['force_items_deletion']): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_href' => "checkout.delete.from_status?cart_id=".($this->_tpl_vars['key']),'but_meta' => "cm-ajax",'but_rev' => 'cart_status','but_role' => 'delete','but_name' => 'delete_cart_item')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
						<p>
							<strong class="valign"><?php echo $this->_tpl_vars['p']['amount']; ?>
</strong>&nbsp;x&nbsp;<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/price.tpl", 'smarty_include_vars' => array('value' => $this->_tpl_vars['p']['display_price'],'span_id' => "price_".($this->_tpl_vars['key']),'class' => 'none')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</p>
					</li>
					<?php if (! ($this->_foreach['cart_products']['iteration'] == $this->_foreach['cart_products']['total'])): ?>
						<li class="delim">&nbsp;</li>
					<?php endif; ?>
					<?php endif; ?>
					<?php endforeach; endif; unset($_from); ?>
					<?php if ($this->_tpl_vars['addons']['gift_certificates']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/gift_certificates/hooks/index/cart_status.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
				</ul>
			<?php else: ?>
				<p class="center"><?php echo fn_get_lang_var('cart_is_empty', $this->getLanguage()); ?>
</p>
			<?php endif; ?>
			</div>
			<div class="buttons-container<?php if ($_SESSION['cart']['amount']): ?> full-cart<?php endif; ?>">
				<a href="<?php echo fn_url("checkout.cart"); ?>
" class="view-cart"><?php echo fn_get_lang_var('view_cart', $this->getLanguage()); ?>
</a>
				<?php if ($this->_tpl_vars['settings']['General']['checkout_redirect'] != 'Y'): ?>
					<a href="<?php echo fn_url("checkout.checkout"); ?>
"><?php echo fn_get_lang_var('checkout', $this->getLanguage()); ?>
</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div><!--/dynamic-->

<div class="checkout-link<?php if ($_SESSION['cart']['amount']): ?> full-cart<?php endif; ?>">

<a href="<?php echo fn_url("checkout.checkout"); ?>
"><?php echo fn_get_lang_var('checkout', $this->getLanguage()); ?>
</a>

</div>
<!--cart_status--></div>