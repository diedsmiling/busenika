<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:45
         compiled from addons/discussion/views/discussion_manager/components/discussion.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'script', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 4, false),array('function', 'cycle', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 19, false),array('modifier', 'fn_get_discussion_posts', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 7, false),array('modifier', 'fn_url', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 9, false),array('modifier', 'escape', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 23, false),array('modifier', 'date_format', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 48, false),array('modifier', 'fn_check_view_permissions', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 84, false),array('block', 'hook', 'addons/discussion/views/discussion_manager/components/discussion.tpl', 20, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('ip_address','rating','excellent','very_good','average','fair','poor','delete','disapprove','approve','approved','not_approved','no_data','delete_selected','choose_action','add_post','general','name','your_rating','excellent','very_good','average','fair','poor','your_message','add','new_post'));
?>

<?php if ($this->_tpl_vars['discussion']): ?>
<?php echo smarty_function_script(array('src' => "js/picker.js"), $this);?>


<div class="cm-hide-save-button" id="content_discussion">
<?php $this->assign('posts', fn_get_discussion_posts($this->_tpl_vars['discussion']['thread_id'], $this->_tpl_vars['_REQUEST']['page']), false); ?>

<form action="<?php echo fn_url(""); ?>
" method="POST" class="cm-form-highlight" name="update_posts_form">
<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
&amp;selected_section=discussion" />
<input type="hidden" name="selected_section" value="" />

<?php if ($this->_tpl_vars['posts']): ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array('id' => 'pagination_discussion')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div class="posts-container">

<?php $_from = $this->_tpl_vars['posts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['post']):
?>
<div class="<?php echo smarty_function_cycle(array('values' => "manage-row, "), $this);?>
 posts <?php if ($this->_tpl_vars['discussion']['object_type'] == 'O'): ?><?php if ($this->_tpl_vars['post']['user_id'] == $this->_tpl_vars['user_id']): ?>incoming<?php else: ?>outgoing<?php endif; ?><?php endif; ?>">
<?php $this->_tag_stack[] = array('hook', array('name' => "discussion:items_list_row")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<div class="clear">
		<div class="valign float-left">
			<input type="text" name="posts[<?php echo $this->_tpl_vars['post']['post_id']; ?>
][name]" value="<?php echo smarty_modifier_escape($this->_tpl_vars['post']['name']); ?>
" size="40" class="input-text valign strong" /><span class="valign">&nbsp;|&nbsp;<?php echo fn_get_lang_var('ip_address', $this->getLanguage()); ?>
:&nbsp;<?php echo $this->_tpl_vars['post']['ip_address']; ?>
</span>
		</div>
		<?php if ($this->_tpl_vars['discussion']['type'] == 'R' || $this->_tpl_vars['discussion']['type'] == 'B'): ?>
		<div class="float-right">

			<strong class="valign"><?php echo fn_get_lang_var('rating', $this->getLanguage()); ?>
:</strong>
			<select class="valign" name="posts[<?php echo $this->_tpl_vars['post']['post_id']; ?>
][rating_value]">
				<option value="5" <?php if ($this->_tpl_vars['post']['rating_value'] == '5'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('excellent', $this->getLanguage()); ?>
</option>
				<option value="4" <?php if ($this->_tpl_vars['post']['rating_value'] == '4'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('very_good', $this->getLanguage()); ?>
</option>
				<option value="3" <?php if ($this->_tpl_vars['post']['rating_value'] == '3'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('average', $this->getLanguage()); ?>
</option>
				<option value="2" <?php if ($this->_tpl_vars['post']['rating_value'] == '2'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('fair', $this->getLanguage()); ?>
</option>
				<option value="1" <?php if ($this->_tpl_vars['post']['rating_value'] == '1'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('poor', $this->getLanguage()); ?>
</option>
			</select>

		</div>
		<?php endif; ?>
	</div>

	<?php $this->_tag_stack[] = array('hook', array('name' => "discussion:update_post")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php if ($this->_tpl_vars['discussion']['type'] == 'C' || $this->_tpl_vars['discussion']['type'] == 'B'): ?>
		<textarea name="posts[<?php echo $this->_tpl_vars['post']['post_id']; ?>
][message]" class="input-textarea-long" cols="80" rows="5"><?php echo smarty_modifier_escape($this->_tpl_vars['post']['message']); ?>
</textarea>
	<?php endif; ?>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<p>
	<span class="strong italic"><?php echo smarty_modifier_date_format($this->_tpl_vars['post']['timestamp'], ($this->_tpl_vars['settings']['Appearance']['date_format']).", ".($this->_tpl_vars['settings']['Appearance']['time_format'])); ?>
</span>
	&nbsp;-&nbsp;
	<?php echo '[&nbsp;&nbsp<span class="select-field"><input type="checkbox" name="delete_posts['; ?><?php echo $this->_tpl_vars['post']['post_id']; ?><?php echo ']" id="delete_checkbox_'; ?><?php echo $this->_tpl_vars['post']['post_id']; ?><?php echo '"  class="checkbox cm-item" value="Y" /><label for="delete_checkbox_'; ?><?php echo $this->_tpl_vars['post']['post_id']; ?><?php echo '">'; ?><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?><?php echo '</label></span>'; ?><?php if ($this->_tpl_vars['discussion']['object_type'] != 'O'): ?><?php echo '|&nbsp;&nbsp;<span class="select-field"><input type="hidden" name="posts['; ?><?php echo $this->_tpl_vars['post']['post_id']; ?><?php echo '][status]" value="'; ?><?php echo $this->_tpl_vars['post']['status']; ?><?php echo '" /><input type="checkbox" class="checkbox" name="posts['; ?><?php echo $this->_tpl_vars['post']['post_id']; ?><?php echo '][status]" id="dis_approve_post_'; ?><?php echo $this->_tpl_vars['post']['post_id']; ?><?php echo '" value="'; ?><?php if ($this->_tpl_vars['post']['status'] == 'A'): ?><?php echo 'D'; ?><?php else: ?><?php echo 'A'; ?><?php endif; ?><?php echo '" /><label for="dis_approve_post_'; ?><?php echo $this->_tpl_vars['post']['post_id']; ?><?php echo '">'; ?><?php if ($this->_tpl_vars['post']['status'] == 'A'): ?><?php echo ''; ?><?php echo fn_get_lang_var('disapprove', $this->getLanguage()); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php echo fn_get_lang_var('approve', $this->getLanguage()); ?><?php echo ''; ?><?php endif; ?><?php echo '</label></span>'; ?><?php endif; ?><?php echo ']'; ?><?php if ($this->_tpl_vars['discussion']['object_type'] != 'O'): ?><?php echo '&nbsp;-&nbsp;'; ?><?php if ($this->_tpl_vars['post']['status'] == 'A'): ?><?php echo '<span class="approved-text">'; ?><?php echo fn_get_lang_var('approved', $this->getLanguage()); ?><?php echo ''; ?><?php else: ?><?php echo '<span class="not-approved-text">'; ?><?php echo fn_get_lang_var('not_approved', $this->getLanguage()); ?><?php echo ''; ?><?php endif; ?><?php echo '</span>'; ?><?php endif; ?><?php echo ''; ?>

</p>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
</div>
<?php endforeach; endif; unset($_from); ?>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array('id' => 'pagination_discussion')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php else: ?>
	<p class="no-items"><?php echo fn_get_lang_var('no_data', $this->getLanguage()); ?>
</p>
<?php endif; ?>

<div class="buttons-container buttons-bg">
	<?php if ($this->_tpl_vars['posts']): ?>
	<div class="float-left">
		<?php ob_start(); ?>
		<ul>
			<li><a name="dispatch[discussion.delete_posts]" class="cm-process-items cm-confirm" rev="update_posts_form"><?php echo fn_get_lang_var('delete_selected', $this->getLanguage()); ?>
</a></li>
		</ul>
		<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[discussion.update_posts]",'but_role' => 'button_main')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('prefix' => 'main','hide_actions' => true,'tools_list' => $this->_smarty_vars['capture']['tools_list'],'display' => 'inline','link_text' => fn_get_lang_var('choose_action', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>

	<?php if (fn_check_view_permissions('discussion_manager')): ?>
	<div class="float-right">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_post','link_text' => fn_get_lang_var('add_post', $this->getLanguage()),'act' => 'general')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>
</div>
</form>

<?php if (fn_check_view_permissions('discussion_manager')): ?>
	<?php ob_start(); ?>
	<form action="<?php echo fn_url(""); ?>
" method="POST" name="add_post_form" class="cm-form-highlight">
	<div class="object-container">
		<div class="tabs cm-j-tabs">
			<ul>
				<li id="tab_add_post" class="cm-js cm-active"><a><?php echo fn_get_lang_var('general', $this->getLanguage()); ?>
</a></li>
			</ul>
		</div>

		<div class="cm-tabs-content" id="content_tab_add_post">
		<input type ="hidden" name="post_data[thread_id]" value="<?php echo $this->_tpl_vars['discussion']['thread_id']; ?>
" />
		<input type ="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
&amp;selected_section=discussion" />

		<div class="form-field">
			<label for="post_data_name" class="cm-required"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
			<input type="text" name="post_data[name]" id="post_data_name" value="<?php if ($this->_tpl_vars['auth']['user_id']): ?><?php echo $this->_tpl_vars['user_info']['firstname']; ?>
 <?php echo $this->_tpl_vars['user_info']['lastname']; ?>
<?php endif; ?>" size="40" class="input-text-large main-input" />
		</div>

		<?php if ($this->_tpl_vars['discussion']['type'] == 'R' || $this->_tpl_vars['discussion']['type'] == 'B'): ?>
		<div class="form-field">
			<label for="rating_value"><?php echo fn_get_lang_var('your_rating', $this->getLanguage()); ?>
:</label>
			<select name="post_data[rating_value]" id="rating_value">
				<option value="5" selected="selected"><?php echo fn_get_lang_var('excellent', $this->getLanguage()); ?>
</option>
				<option value="4"><?php echo fn_get_lang_var('very_good', $this->getLanguage()); ?>
</option>
				<option value="3"><?php echo fn_get_lang_var('average', $this->getLanguage()); ?>
</option>
				<option value="2"><?php echo fn_get_lang_var('fair', $this->getLanguage()); ?>
</option>
				<option value="1"><?php echo fn_get_lang_var('poor', $this->getLanguage()); ?>
</option>
			</select>
		</div>
		<?php endif; ?>

		<?php $this->_tag_stack[] = array('hook', array('name' => "discussion:add_post")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php if ($this->_tpl_vars['discussion']['type'] == 'C' || $this->_tpl_vars['discussion']['type'] == 'B'): ?>
		<div class="form-field">
			<label for="message"><?php echo fn_get_lang_var('your_message', $this->getLanguage()); ?>
:</label>
			<textarea name="post_data[message]" id="message" class="input-textarea-long" cols="70" rows="8"></textarea>
		</div>
		<?php endif; ?>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		</div>
	</div>

	<div class="buttons-container">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('add', $this->getLanguage()),'but_name' => "dispatch[discussion.add_post]",'cancel_action' => 'close')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	</form>
	<?php $this->_smarty_vars['capture']['add_new_picker'] = ob_get_contents(); ob_end_clean(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/popupbox.tpl", 'smarty_include_vars' => array('id' => 'add_new_post','text' => fn_get_lang_var('new_post', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['add_new_picker'],'act' => 'fake')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

</div>

<?php endif; ?><?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>