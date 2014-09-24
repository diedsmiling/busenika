<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:12
         compiled from common_templates/pagination.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'common_templates/pagination.tpl', 4, false),array('modifier', 'fn_query_remove', 'common_templates/pagination.tpl', 5, false),array('modifier', 'escape', 'common_templates/pagination.tpl', 35, false),array('modifier', 'fn_url', 'common_templates/pagination.tpl', 43, false),array('function', 'script', 'common_templates/pagination.tpl', 9, false),array('function', 'math', 'common_templates/pagination.tpl', 71, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('go_to_page','go','go','previous','next','total_items','items_per_page'));
?>

<?php if ($this->_tpl_vars['pagination']): ?>
<?php $this->assign('id', smarty_modifier_default(@$this->_tpl_vars['div_id'], 'pagination_contents'), false); ?>
<?php $this->assign('qstring', fn_query_remove($_SERVER['QUERY_STRING'], 'page', 'result_ids'), false); ?>

<?php if ($this->_smarty_vars['capture']['pagination_open'] != 'Y'): ?>
	<?php if ($this->_tpl_vars['settings']['DHTML']['admin_ajax_based_pagination'] == 'Y' && $this->_tpl_vars['pagination']['total_pages'] > 1): ?>
		<?php echo smarty_function_script(array('src' => "js/jquery.history.js"), $this);?>

	<?php endif; ?>
<div id="<?php echo $this->_tpl_vars['id']; ?>
">

<?php if ($this->_tpl_vars['save_current_page']): ?>
	<input type="hidden" name="page" value="<?php echo smarty_modifier_default(smarty_modifier_default(@$this->_tpl_vars['search']['page'], @$this->_tpl_vars['_REQUEST']['page']), 1); ?>
" />
<?php endif; ?>

<?php if ($this->_tpl_vars['save_current_url']): ?>
	<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
" />
<?php endif; ?>

<?php endif; ?>

<?php if ($this->_tpl_vars['settings']['DHTML']['admin_ajax_based_pagination'] == 'Y'): ?>
	<?php $this->assign('ajax_class', "cm-ajax", false); ?>
<?php endif; ?>
<?php if (! $this->_tpl_vars['disable_history']): ?>
	<?php $this->assign('history_class', " cm-history", false); ?>
<?php else: ?>
	<?php $this->assign('history_class', " cm-ajax-cache", false); ?>
<?php endif; ?>

<div class="pagination clear cm-pagination-wraper<?php if ($this->_smarty_vars['capture']['pagination_open'] != 'Y'): ?> top-pagination<?php endif; ?>">
	<?php if ($this->_tpl_vars['pagination']['total_pages'] > 1): ?>
	<div class="float-left">
		<label><?php echo smarty_modifier_escape(fn_get_lang_var('go_to_page', $this->getLanguage()), 'html'); ?>
:</label>
		<input type="text" class="input-text-short valign cm-pagination<?php echo $this->_tpl_vars['history_class']; ?>
" value="<?php if ($this->_tpl_vars['_REQUEST']['page'] > $this->_tpl_vars['pagination']['total_pages']): ?>1<?php else: ?><?php echo smarty_modifier_default(@$this->_tpl_vars['_REQUEST']['page'], 1); ?>
<?php endif; ?>" />
		<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/pg_right_arrow.gif" class="pagination-go-button hand cm-pagination-button" alt="<?php echo smarty_modifier_escape(fn_get_lang_var('go', $this->getLanguage()), 'html'); ?>
" title="<?php echo smarty_modifier_escape(fn_get_lang_var('go', $this->getLanguage()), 'html'); ?>
" />
	</div>
	<?php endif; ?>

	<div class="float-right">
	<?php if ($this->_tpl_vars['pagination']['current_page'] != 'full_list' && $this->_tpl_vars['pagination']['total_pages'] > 1): ?>
		<span class="lowercase"><a name="pagination" class="<?php if ($this->_tpl_vars['pagination']['prev_page']): ?><?php echo $this->_tpl_vars['ajax_class']; ?>
<?php endif; ?><?php echo $this->_tpl_vars['history_class']; ?>
" <?php if ($this->_tpl_vars['pagination']['prev_page']): ?>href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pagination']['prev_page'])); ?>
" rel="<?php echo $this->_tpl_vars['pagination']['prev_page']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?>>&laquo;&nbsp;<?php echo fn_get_lang_var('previous', $this->getLanguage()); ?>
</a></span>

		<?php $_from = $this->_tpl_vars['pagination']['navi_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['f_pg'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['f_pg']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['pg']):
        $this->_foreach['f_pg']['iteration']++;
?>
			<?php if (($this->_foreach['f_pg']['iteration'] <= 1) && $this->_tpl_vars['pg'] > 1): ?>
			<a name="pagination" class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php echo $this->_tpl_vars['history_class']; ?>
" href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=1`"); ?>
" rel="1" rev="<?php echo $this->_tpl_vars['id']; ?>
">1</a>
			<?php if ($this->_tpl_vars['pg'] != 2): ?><a name="pagination" class="<?php if ($this->_tpl_vars['pagination']['prev_range']): ?><?php echo $this->_tpl_vars['ajax_class']; ?>
<?php endif; ?> prev-range<?php echo $this->_tpl_vars['history_class']; ?>
" <?php if ($this->_tpl_vars['pagination']['prev_range']): ?>href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pagination']['prev_range'])); ?>
" rel="<?php echo $this->_tpl_vars['pagination']['prev_range']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?>>&nbsp;...&nbsp;</a><?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['pg'] != $this->_tpl_vars['pagination']['current_page']): ?><a name="pagination" class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php echo $this->_tpl_vars['history_class']; ?>
" href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pg'])); ?>
" rel="<?php echo $this->_tpl_vars['pg']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['pg']; ?>
</a><?php else: ?><strong><?php echo $this->_tpl_vars['pg']; ?>
</strong><?php endif; ?>
			<?php if (($this->_foreach['f_pg']['iteration'] == $this->_foreach['f_pg']['total']) && $this->_tpl_vars['pg'] < $this->_tpl_vars['pagination']['total_pages']): ?>
			<?php if ($this->_tpl_vars['pg'] != $this->_tpl_vars['pagination']['total_pages']-1): ?><a name="pagination" class="<?php if ($this->_tpl_vars['pagination']['next_range']): ?><?php echo $this->_tpl_vars['ajax_class']; ?>
<?php endif; ?> next-range<?php echo $this->_tpl_vars['history_class']; ?>
" <?php if ($this->_tpl_vars['pagination']['next_range']): ?>href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pagination']['next_range'])); ?>
" rel="<?php echo $this->_tpl_vars['pagination']['next_range']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?>>&nbsp;...&nbsp;</a><?php endif; ?><a name="pagination" class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php echo $this->_tpl_vars['history_class']; ?>
" href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pagination']['total_pages'])); ?>
" rel="<?php echo $this->_tpl_vars['pagination']['total_pages']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['pagination']['total_pages']; ?>
</a>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>

		<span class="lowercase"><a name="pagination" class="<?php if ($this->_tpl_vars['pagination']['next_page']): ?><?php echo $this->_tpl_vars['ajax_class']; ?>
<?php endif; ?><?php echo $this->_tpl_vars['history_class']; ?>
" <?php if ($this->_tpl_vars['pagination']['next_page']): ?>href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['qstring'])."&amp;page=".($this->_tpl_vars['pagination']['next_page'])); ?>
" rel="<?php echo $this->_tpl_vars['pagination']['next_page']; ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?>><?php echo fn_get_lang_var('next', $this->getLanguage()); ?>
&nbsp;&raquo;</a></span>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['pagination']): ?>
		<?php if ($this->_tpl_vars['pagination']['total_items']): ?>
			&nbsp;<?php echo fn_get_lang_var('total_items', $this->getLanguage()); ?>
:&nbsp;<strong><?php echo $this->_tpl_vars['pagination']['total_items']; ?>
&nbsp;/</strong>
			
			<?php ob_start(); ?>
				<ul>
					<li class="strong"><?php echo fn_get_lang_var('items_per_page', $this->getLanguage()); ?>
:</li>
					<?php $this->assign('range_url', fn_query_remove($this->_tpl_vars['qstring'], 'items_per_page'), false); ?>
					<?php $_from = $this->_tpl_vars['pagination']['per_page_range']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['step']):
?>
						<li><a name="pagination" class="<?php echo $this->_tpl_vars['ajax_class']; ?>
" href="<?php echo fn_url(($this->_tpl_vars['index_script'])."?".($this->_tpl_vars['range_url'])."&amp;items_per_page=".($this->_tpl_vars['step'])); ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
"><?php echo $this->_tpl_vars['step']; ?>
</a></li>
					<?php endforeach; endif; unset($_from); ?>
				</ul>
			<?php $this->_smarty_vars['capture']['pagination_list'] = ob_get_contents(); ob_end_clean(); ?>
			<?php echo smarty_function_math(array('equation' => "rand()",'assign' => 'rnd'), $this);?>

			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('prefix' => "pagination_".($this->_tpl_vars['rnd']),'hide_actions' => true,'tools_list' => $this->_smarty_vars['capture']['pagination_list'],'display' => 'inline','link_text' => $this->_tpl_vars['pagination']['items_per_page'],'override_meta' => "pagination-selector")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
	<?php endif; ?>
	</div>
</div>

<?php if ($this->_smarty_vars['capture']['pagination_open'] == 'Y'): ?>
	<!--<?php echo $this->_tpl_vars['id']; ?>
--></div>
	<?php ob_start(); ?>N<?php $this->_smarty_vars['capture']['pagination_open'] = ob_get_contents(); ob_end_clean(); ?>
<?php elseif ($this->_smarty_vars['capture']['pagination_open'] != 'Y'): ?>
	<?php ob_start(); ?>Y<?php $this->_smarty_vars['capture']['pagination_open'] = ob_get_contents(); ob_end_clean(); ?>
<?php endif; ?>

<?php endif; ?><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>