<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:45
         compiled from common_templates/pagination.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'common_templates/pagination.tpl', 6, false),array('modifier', 'fn_query_remove', 'common_templates/pagination.tpl', 24, false),array('modifier', 'escape', 'common_templates/pagination.tpl', 24, false),array('modifier', 'fn_url', 'common_templates/pagination.tpl', 33, false),array('function', 'script', 'common_templates/pagination.tpl', 9, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('navi_pages'));
?>
<?php  ob_start();  ?><?php if(empty($_GET['no_pagination'])): ?>



<?php $this->assign('id', smarty_modifier_default(@$this->_tpl_vars['id'], 'pagination_contents'), false); ?>
<?php if ($this->_smarty_vars['capture']['pagination_open'] != 'Y'): ?>
	<?php if ($this->_tpl_vars['settings']['DHTML']['customer_ajax_based_pagination'] == 'Y' && $this->_tpl_vars['pagination']['total_pages'] > 1): ?>
		<?php echo smarty_function_script(array('src' => "js/jquery.history.js"), $this);?>

	<?php endif; ?>
	<div class="pagination-container" id="<?php echo $this->_tpl_vars['id']; ?>
">

	<?php if ($this->_tpl_vars['save_current_page']): ?>
	<input type="hidden" name="page" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['search']['page'], @$this->_tpl_vars['_REQUEST']['page']); ?>
" />
	<?php endif; ?>

	<?php if ($this->_tpl_vars['save_current_url']): ?>
	<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
" />
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['pagination']['total_pages'] > 1): ?>
	<?php if ($this->_tpl_vars['settings']['Appearance']['top_pagination'] == 'Y' && $this->_smarty_vars['capture']['pagination_open'] != 'Y' || $this->_smarty_vars['capture']['pagination_open'] == 'Y'): ?>
	<?php $this->assign('qstring', smarty_modifier_escape(fn_query_remove($_SERVER['QUERY_STRING'], 'page', 'result_ids')), false); ?>
	<?php if ($this->_tpl_vars['settings']['DHTML']['customer_ajax_based_pagination'] == 'Y'): ?>
		<?php $this->assign('ajax_class', "cm-ajax", false); ?>
	<?php endif; ?>

	<div class="pagination cm-pagination-wraper center">
		<?php echo fn_get_lang_var('navi_pages', $this->getLanguage()); ?>
:&nbsp;&nbsp;
	
		<?php if ($this->_tpl_vars['pagination']['prev_range']): ?>
			<a name="pagination" href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pagination']['prev_range'])); ?>
" rel="<?php echo $this->_tpl_vars['pagination']['prev_range']; ?>
" class="cm-history <?php echo $this->_tpl_vars['ajax_class']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
">...</a>
		<?php endif; ?>

		<?php $_from = $this->_tpl_vars['pagination']['navi_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['pg']):
?>
			<?php if ($this->_tpl_vars['pg'] != $this->_tpl_vars['pagination']['current_page']): ?>
				<a name="pagination" href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pg'])); ?>
" rel="<?php echo $this->_tpl_vars['pg']; ?>
" class="cm-history <?php echo $this->_tpl_vars['ajax_class']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['pg']; ?>
</a>
			<?php else: ?>
				<strong class="pagination-selected-page"><?php echo $this->_tpl_vars['pg']; ?>
</strong>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>

		<?php if ($this->_tpl_vars['pagination']['next_range']): ?>
			<a name="pagination" href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pagination']['next_range'])); ?>
" rel="<?php echo $this->_tpl_vars['pagination']['next_range']; ?>
" class="cm-history <?php echo $this->_tpl_vars['ajax_class']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
">...</a>
		<?php endif; ?>
	</div>
	<?php else: ?>
	<div class="cm-pagination-wraper"></div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->_smarty_vars['capture']['pagination_open'] == 'Y'): ?>
	<!--<?php echo $this->_tpl_vars['id']; ?>
--></div>
	<?php ob_start(); ?>N<?php $this->_smarty_vars['capture']['pagination_open'] = ob_get_contents(); ob_end_clean(); ?>
<?php elseif ($this->_smarty_vars['capture']['pagination_open'] != 'Y'): ?>
	<?php ob_start(); ?>Y<?php $this->_smarty_vars['capture']['pagination_open'] = ob_get_contents(); ob_end_clean(); ?>
<?php endif; ?>
<?php endif; ?>
<?php  ob_end_flush();  ?>