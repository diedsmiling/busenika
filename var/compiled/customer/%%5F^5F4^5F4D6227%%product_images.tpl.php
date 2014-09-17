<?php /* Smarty version 2.6.18, created on 2014-09-17 00:24:29
         compiled from views/products/components/product_images.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'reset', 'views/products/components/product_images.tpl', 8, false),array('modifier', 'count', 'views/products/components/product_images.tpl', 50, false),array('function', 'script', 'views/products/components/product_images.tpl', 45, false),)), $this); ?>

<?php $this->assign('th_size', '30', false); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/previewer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['product']['main_pair']['icon'] || $this->_tpl_vars['product']['main_pair']['detailed']): ?>
	<?php $this->assign('image_pair_var', $this->_tpl_vars['product']['main_pair'], false); ?>
<?php elseif ($this->_tpl_vars['product']['option_image_pairs']): ?>
	<?php $this->assign('image_pair_var', reset($this->_tpl_vars['product']['option_image_pairs']), false); ?>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('obj_id' => $this->_tpl_vars['product']['product_id'],'images' => $this->_tpl_vars['image_pair_var'],'object_type' => 'detailed_product','class' => "cm-thumbnails",'show_thumbnail' => 'Y','image_width' => $this->_tpl_vars['settings']['Thumbnails']['product_details_thumbnail_width'],'image_height' => $this->_tpl_vars['settings']['Thumbnails']['product_details_thumbnail_height'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_from = $this->_tpl_vars['product']['image_pairs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['image_pair']):
?>
	<?php if ($this->_tpl_vars['image_pair']): ?>
		<?php if ($this->_tpl_vars['image_pair']['image_id'] == 0): ?>
			<?php $this->assign('image_id', $this->_tpl_vars['image_pair']['detailed_id'], false); ?>
		<?php else: ?>
			<?php $this->assign('image_id', $this->_tpl_vars['image_pair']['image_id'], false); ?>
		<?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('images' => $this->_tpl_vars['image_pair'],'object_type' => 'detailed_product','class' => "cm-thumbnails hidden",'show_thumbnail' => 'Y','detailed_link_class' => 'hidden','obj_id' => ($this->_tpl_vars['product']['product_id'])."_".($this->_tpl_vars['image_id']),'image_width' => $this->_tpl_vars['settings']['Thumbnails']['product_details_thumbnail_width'],'image_height' => $this->_tpl_vars['settings']['Thumbnails']['product_details_thumbnail_height'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php if ($this->_tpl_vars['image_pair_var'] && $this->_tpl_vars['product']['image_pairs']): ?>
	<?php if ($this->_tpl_vars['settings']['Appearance']['thumbnails_gallery'] == 'Y'): ?>
	<?php echo '<ul id="product_thumbnails" class="center jcarousel-skin hidden"><li><a class="cm-thumbnails-mini">'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('images' => $this->_tpl_vars['image_pair_var'],'object_type' => 'detailed_product','link_class' => "cm-thumbnails-mini cm-cur-item",'image_width' => $this->_tpl_vars['th_size'],'image_height' => $this->_tpl_vars['th_size'],'show_thumbnail' => 'Y','show_detailed_link' => false,'obj_id' => ($this->_tpl_vars['product']['product_id'])."_mini",'make_box' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '</a></li>'; ?><?php $_from = $this->_tpl_vars['product']['image_pairs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['image_pair']):
?><?php echo ''; ?><?php if ($this->_tpl_vars['image_pair']): ?><?php echo '<li>'; ?><?php if ($this->_tpl_vars['image_pair']['image_id'] == 0): ?><?php echo ''; ?><?php $this->assign('image_id', $this->_tpl_vars['image_pair']['detailed_id'], false); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php $this->assign('image_id', $this->_tpl_vars['image_pair']['image_id'], false); ?><?php echo ''; ?><?php endif; ?><?php echo '<a class="cm-thumbnails-mini">'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('images' => $this->_tpl_vars['image_pair'],'object_type' => 'detailed_product','link_class' => "cm-thumbnails-mini",'image_width' => $this->_tpl_vars['th_size'],'image_height' => $this->_tpl_vars['th_size'],'show_thumbnail' => 'Y','show_detailed_link' => false,'obj_id' => ($this->_tpl_vars['product']['product_id'])."_".($this->_tpl_vars['image_id'])."_mini",'make_box' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '</a></li>'; ?><?php endif; ?><?php echo ''; ?><?php endforeach; endif; unset($_from); ?><?php echo '</ul>'; ?>

		
		<?php echo smarty_function_script(array('src' => "js/jquery.jcarousel.js"), $this);?>

		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function() <?php echo $this->_tpl_vars['ldelim']; ?>

			$('#product_thumbnails').show();
			<?php if (count($this->_tpl_vars['product']['image_pairs']) > 2): ?>
				$('#product_thumbnails').removeClass('hidden');
				var i_width = $('.cm-thumbnails-mini').outerWidth(true);
				var c_width = i_width * 3;
				var i_height = $('.cm-thumbnails-mini').outerHeight(true);
				$('#product_thumbnails').jcarousel(<?php echo $this->_tpl_vars['ldelim']; ?>

					scroll: 1,
					wrap: 'circular',
					animation: 'fast',
					initCallback: fn_scroller_init_callback,
					itemVisibleOutCallback: <?php echo $this->_tpl_vars['ldelim']; ?>
onAfterAnimation: fn_scroller_next_callback, onBeforeAnimation: fn_scroller_prev_callback<?php echo $this->_tpl_vars['rdelim']; ?>
,
					item_width: i_width,
					item_height: i_height,
					clip_width: c_width,
					clip_height: i_height,
					buttonNextHTML: '<div></div>',
					buttonPrevHTML: '<div></div>',
					buttonNextEvent: 'click',
					buttonPrevEvent: 'click',
					item_count: <?php echo count($this->_tpl_vars['product']['image_pairs']); ?>
 + 1
				<?php echo $this->_tpl_vars['rdelim']; ?>
);
				$('.jcarousel-skin').css(<?php echo $this->_tpl_vars['ldelim']; ?>
'width': c_width + $('.jcarousel-prev-horizontal').outerWidth(true) * 2 + 'px'<?php echo $this->_tpl_vars['rdelim']; ?>
);
			<?php endif; ?>
		<?php echo $this->_tpl_vars['rdelim']; ?>
);
		//]]>
		</script>
	<?php else: ?>
		<div class="center" style="width: <?php echo $this->_tpl_vars['settings']['Thumbnails']['product_details_thumbnail_width']; ?>
px;">
		<?php echo '<a class="cm-thumbnails-mini">'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('images' => $this->_tpl_vars['image_pair_var'],'object_type' => 'detailed_product','link_class' => "cm-thumbnails-mini cm-cur-item",'image_width' => $this->_tpl_vars['th_size'],'image_height' => $this->_tpl_vars['th_size'],'show_thumbnail' => 'Y','show_detailed_link' => false,'obj_id' => ($this->_tpl_vars['product']['product_id'])."_mini",'make_box' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '</a>'; ?><?php $_from = $this->_tpl_vars['product']['image_pairs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['image_pair']):
?><?php echo ''; ?><?php if ($this->_tpl_vars['image_pair']): ?><?php echo ''; ?><?php if ($this->_tpl_vars['image_pair']['image_id'] == 0): ?><?php echo ''; ?><?php $this->assign('image_id', $this->_tpl_vars['image_pair']['detailed_id'], false); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php $this->assign('image_id', $this->_tpl_vars['image_pair']['image_id'], false); ?><?php echo ''; ?><?php endif; ?><?php echo '<a class="cm-thumbnails-mini">'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image.tpl", 'smarty_include_vars' => array('images' => $this->_tpl_vars['image_pair'],'object_type' => 'detailed_product','link_class' => "cm-thumbnails-mini",'image_width' => $this->_tpl_vars['th_size'],'show_thumbnail' => 'Y','show_detailed_link' => false,'obj_id' => ($this->_tpl_vars['product']['product_id'])."_".($this->_tpl_vars['image_id'])."_mini",'make_box' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php echo '</a>'; ?><?php endif; ?><?php echo ''; ?><?php endforeach; endif; unset($_from); ?><?php echo ''; ?>

	    </div>
	<?php endif; ?>
<?php endif; ?>