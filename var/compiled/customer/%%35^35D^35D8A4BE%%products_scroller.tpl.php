<?php /* Smarty version 2.6.18, created on 2014-09-23 21:20:59
         compiled from blocks/products_scroller.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_block_scroller_directions', 'blocks/products_scroller.tpl', 7, false),array('modifier', 'to_json', 'blocks/products_scroller.tpl', 7, false),array('modifier', 'fn_url', 'blocks/products_scroller.tpl', 35, false),array('function', 'math', 'blocks/products_scroller.tpl', 25, false),array('function', 'script', 'blocks/products_scroller.tpl', 56, false),)), $this); ?>

<?php if ($this->_tpl_vars['scrollers_initialization'] != 'Y'): ?>
<script type="text/javascript">
//<![CDATA[
var scroller_directions = <?php echo smarty_modifier_to_json(fn_get_block_scroller_directions("")); ?>
;
var scrollers_list = [];
//]]>
</script>
<?php ob_start(); ?>Y<?php $this->_smarty_vars['capture']['scrollers_initialization'] = ob_get_contents(); ob_end_clean(); ?>
<?php endif; ?>

<?php $this->assign('obj_prefix', ($this->_tpl_vars['block']['block_id'])."000", false); ?>

<?php $this->assign('item_width', '140', false); ?>
<?php $this->assign('delim_height', '20', false); ?>

<div align="center">
	<ul id="scroll_list_<?php echo $this->_tpl_vars['block']['block_id']; ?>
" class="jcarousel-skin hidden">
		<?php $this->assign('image_h', '123', false); ?>
		<?php $this->assign('text_h', '90', false); ?>
		<?php $this->assign('cellspacing', '2', false); ?>

		<?php echo smarty_function_math(array('equation' => "3 * cellspacing + image_h + text_h",'assign' => 'item_height','cellspacing' => $this->_tpl_vars['cellspacing'],'image_h' => $this->_tpl_vars['image_h'],'text_h' => $this->_tpl_vars['text_h']), $this);?>


		<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['for_products'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['for_products']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['product']):
        $this->_foreach['for_products']['iteration']++;
?>
			<li>
			<?php $this->assign('obj_id', "scr_".($this->_tpl_vars['block']['block_id'])."000".($this->_tpl_vars['product']['product_id']), false); ?>
			<?php $this->assign('img_object_type', 'product', false); ?>
			<?php ob_start(); $this->_in_capture[] = '81444c5b101b159dfd3996f2f59e6136';
$_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('image_width' => $this->_tpl_vars['block']['properties']['thumbnail_width'],'image_height' => 120,'images' => $this->_tpl_vars['product']['main_pair'],'no_ids' => true,'object_type' => $this->_tpl_vars['img_object_type'],'show_thumbnail' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
$this->_tpl_vars['object_img'] = ob_get_contents(); ob_end_clean(); array_pop($this->_in_capture); if (!empty($this->_scripts['81444c5b101b159dfd3996f2f59e6136'])) { echo implode("\n", $this->_scripts['81444c5b101b159dfd3996f2f59e6136']); unset($this->_scripts['81444c5b101b159dfd3996f2f59e6136']); }
 ?>
			<table cellpadding="0" cellspacing="<?php echo $this->_tpl_vars['cellspacing']; ?>
" border="0" width="<?php echo $this->_tpl_vars['item_width']; ?>
">
			<tr>
				<td class="center product-image" style="height: <?php echo $this->_tpl_vars['image_h']; ?>
px;">
					<a href="<?php echo fn_url("products.view?product_id=".($this->_tpl_vars['product']['product_id'])); ?>
"><?php echo $this->_tpl_vars['object_img']; ?>
</a></td>
			</tr>
			<tr>
				<td class="center compact" style="height: <?php echo $this->_tpl_vars['text_h']; ?>
px;">
					<?php if ($this->_tpl_vars['block']['properties']['hide_add_to_cart_button'] == 'Y'): ?>
						<?php $this->assign('_show_add_to_cart', false, false); ?>
					<?php else: ?>
						<?php $this->assign('_show_add_to_cart', true, false); ?>
					<?php endif; ?>
					<?php echo ''; ?><?php if ($this->_tpl_vars['block']['properties']['item_number'] == 'Y'): ?><?php echo ''; ?><?php echo $this->_foreach['for_products']['iteration']; ?><?php echo '.&nbsp;'; ?><?php endif; ?><?php echo ''; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "blocks/list_templates/simple_list.tpl", 'smarty_include_vars' => array('product' => $this->_tpl_vars['product'],'show_trunc_name' => true,'show_price' => true,'show_add_to_cart' => $this->_tpl_vars['_show_add_to_cart'],'but_role' => 'text')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo ''; ?>

				</td>
			</tr>
			</table>
			</li>
		<?php endforeach; endif; unset($_from); ?>
	</ul>
</div>

<?php echo smarty_function_script(array('src' => "js/jquery.jcarousel.js"), $this);?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/scroller_init.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>