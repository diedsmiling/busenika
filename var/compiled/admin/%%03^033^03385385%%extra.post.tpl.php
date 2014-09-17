<?php /* Smarty version 2.6.18, created on 2014-09-16 21:02:22
         compiled from addons/discussion/hooks/index/extra.post.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'addons/discussion/hooks/index/extra.post.tpl', 2, false),array('modifier', 'fn_url', 'addons/discussion/hooks/index/extra.post.tpl', 22, false),array('modifier', 'truncate', 'addons/discussion/hooks/index/extra.post.tpl', 26, false),array('function', 'cycle', 'addons/discussion/hooks/index/extra.post.tpl', 13, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('latest_reviews','edit','comment_by','ip_address','no_items'));
?>
<?php if (! defined('COMPANY_ID')): ?>
<div class="statistics-box communication">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader_statistic.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('latest_reviews', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<div class="statistics-body">
	<?php if ($this->_tpl_vars['latest_posts']): ?>
	<div id="stats_discussion">
	<?php $_from = $this->_tpl_vars['latest_posts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['post']):
?>
	<?php $this->assign('o_type', $this->_tpl_vars['post']['object_type'], false); ?>
	<?php $this->assign('object_name', $this->_tpl_vars['discussion_objects'][$this->_tpl_vars['o_type']], false); ?>
	<?php $this->assign('review_name', "discussion_title_".($this->_tpl_vars['object_name']), false); ?>
	<div class="<?php echo smarty_function_cycle(array('values' => " ,manage-post"), $this);?>
 posts">
		<div class="clear">
			<?php if ($this->_tpl_vars['post']['type'] == 'R' || $this->_tpl_vars['post']['type'] == 'B'): ?>
				<div class="float-left">
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion_manager/components/stars.tpl", 'smarty_include_vars' => array('stars' => $this->_tpl_vars['post']['rating'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
			<?php endif; ?>
			
			<div class="float-right">
			<a class="tool-link valign" href="<?php echo fn_url($this->_tpl_vars['post']['object_data']['url']); ?>
"><?php echo fn_get_lang_var('edit', $this->getLanguage()); ?>
</a>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_role' => 'delete_item','but_href' => "index.delete_post?post_id=".($this->_tpl_vars['post']['post_id']),'but_meta' => "cm-ajax cm-confirm",'but_rev' => 'stats_discussion')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</div>
			
			<?php echo fn_get_lang_var($this->_tpl_vars['object_name'], $this->getLanguage()); ?>
:&nbsp;<a href="<?php echo fn_url($this->_tpl_vars['post']['object_data']['url']); ?>
"><?php echo smarty_modifier_truncate($this->_tpl_vars['post']['object_data']['description'], 70); ?>
</a>
			<span class="lowercase">&nbsp;<?php echo fn_get_lang_var('comment_by', $this->getLanguage()); ?>
</span>&nbsp;<?php echo $this->_tpl_vars['post']['name']; ?>

		</div>
	
		<?php if ($this->_tpl_vars['post']['type'] == 'C' || $this->_tpl_vars['post']['type'] == 'B'): ?>
			<div class="scroll-x"><?php echo $this->_tpl_vars['post']['message']; ?>
</div>
		<?php endif; ?>
		
		<div class="clear">
		<div class="float-left"><strong><?php echo fn_get_lang_var('ip_address', $this->getLanguage()); ?>
:</strong>&nbsp;<?php echo $this->_tpl_vars['post']['ip_address']; ?>
</div>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/index/components/dashboard_status.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>
	<?php endforeach; endif; unset($_from); ?>
	<!--stats_discussion--></div>
	<?php else: ?>
	<p class="no-items"><?php echo fn_get_lang_var('no_items', $this->getLanguage()); ?>
</p>
	<?php endif; ?>
	</div>
</div>
<?php endif; ?>