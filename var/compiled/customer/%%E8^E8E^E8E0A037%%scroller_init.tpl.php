<?php /* Smarty version 2.6.18, created on 2014-09-22 22:00:55
         compiled from common_templates/scroller_init.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'common_templates/scroller_init.tpl', 12, false),array('modifier', 'default', 'common_templates/scroller_init.tpl', 12, false),array('modifier', 'sizeof', 'common_templates/scroller_init.tpl', 40, false),)), $this); ?>
<?php  ob_start();  ?>
<?php if ($this->_tpl_vars['block']['properties']['scroller_direction'] == 'up' || $this->_tpl_vars['block']['properties']['scroller_direction'] == 'left'): ?>
	<?php $this->assign('scroller_direction', 'next', false); ?>
	<?php $this->assign('scroller_event', 'onAfterAnimation', false); ?>
<?php else: ?>
	<?php $this->assign('scroller_direction', 'prev', false); ?>
	<?php $this->assign('scroller_event', 'onBeforeAnimation', false); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['block']['properties']['scroller_direction'] == 'left' || $this->_tpl_vars['block']['properties']['scroller_direction'] == 'right'): ?>
	<?php $this->assign('scroller_vert', 'false', false); ?>
	<?php echo smarty_function_math(array('equation' => "item_quantity * item_width",'assign' => 'clip_width','item_width' => $this->_tpl_vars['item_width'],'item_quantity' => smarty_modifier_default(@$this->_tpl_vars['block']['properties']['item_quantity'], 1)), $this);?>

	<?php $this->assign('clip_height', $this->_tpl_vars['item_height'], false); ?>
<?php else: ?>
	<?php $this->assign('scroller_vert', 'true', false); ?>
	<?php $this->assign('clip_width', $this->_tpl_vars['item_width'], false); ?>
	<?php echo smarty_function_math(array('equation' => "item_quantity * item_height",'assign' => 'clip_height','item_height' => $this->_tpl_vars['item_height'],'item_quantity' => smarty_modifier_default(@$this->_tpl_vars['block']['properties']['item_quantity'], 1)), $this);?>

<?php endif; ?>


<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() <?php echo $this->_tpl_vars['ldelim']; ?>

	$('#scroll_list_<?php echo $this->_tpl_vars['block']['block_id']; ?>
').show();
	$('#scroll_list_<?php echo $this->_tpl_vars['block']['block_id']; ?>
').jcarousel(<?php echo $this->_tpl_vars['ldelim']; ?>

		vertical: <?php echo $this->_tpl_vars['scroller_vert']; ?>
,
		size: null,
		scroll: <?php echo smarty_modifier_default(@$this->_tpl_vars['block']['properties']['item_quantity'], 1); ?>
,
		animation: '<?php echo $this->_tpl_vars['block']['properties']['speed']; ?>
',
		easing: '<?php echo $this->_tpl_vars['block']['properties']['easing']; ?>
',
		auto: '<?php echo $this->_tpl_vars['block']['properties']['pause_delay']; ?>
',
		autoDirection: '<?php echo $this->_tpl_vars['scroller_direction']; ?>
',
		wrap: 'circular',
		initCallback: fn_scroller_init_callback,
		itemVisibleOutCallback: <?php echo $this->_tpl_vars['ldelim']; ?>
<?php echo $this->_tpl_vars['scroller_event']; ?>
: fn_scroller_in_out_callback<?php echo $this->_tpl_vars['rdelim']; ?>
,
		item_width: <?php echo $this->_tpl_vars['item_width']; ?>
,
		item_height: <?php echo $this->_tpl_vars['item_height']; ?>
,
		clip_width: <?php echo $this->_tpl_vars['clip_width']; ?>
,
		clip_height: <?php echo $this->_tpl_vars['clip_height']; ?>
,
		item_count: <?php echo sizeof($this->_tpl_vars['items']); ?>

	<?php echo $this->_tpl_vars['rdelim']; ?>
);
<?php echo $this->_tpl_vars['rdelim']; ?>
);
//]]>
</script><?php  ob_end_flush();  ?>