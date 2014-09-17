<?php /* Smarty version 2.6.18, created on 2014-09-16 21:38:46
         compiled from addons/discussion/views/discussion/view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_discussion', 'addons/discussion/views/discussion/view.tpl', 3, false),array('modifier', 'fn_get_discussion_posts', 'addons/discussion/views/discussion/view.tpl', 14, false),array('modifier', 'fn_get_discussion_rating', 'addons/discussion/views/discussion/view.tpl', 24, false),array('modifier', 'date_format', 'addons/discussion/views/discussion/view.tpl', 28, false),array('modifier', 'escape', 'addons/discussion/views/discussion/view.tpl', 32, false),array('modifier', 'nl2br', 'addons/discussion/views/discussion/view.tpl', 32, false),array('modifier', 'strpos', 'addons/discussion/views/discussion/view.tpl', 53, false),array('modifier', 'fn_url', 'addons/discussion/views/discussion/view.tpl', 56, false),array('function', 'cycle', 'addons/discussion/views/discussion/view.tpl', 19, false),array('block', 'hook', 'addons/discussion/views/discussion/view.tpl', 20, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('no_posts_found','new_post','your_name','your_rating','excellent','very_good','average','fair','poor','your_message','submit'));
?>

<?php $this->assign('discussion', fn_get_discussion($this->_tpl_vars['object_id'], $this->_tpl_vars['object_type']), false); ?>

<?php if ($this->_tpl_vars['discussion'] && $this->_tpl_vars['discussion']['type'] != 'D'): ?>

<div id="content_discussion">
<?php if ($this->_tpl_vars['wrap'] == true): ?>
<p>&nbsp;</p>
<?php ob_start(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['title'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $this->assign('posts', fn_get_discussion_posts($this->_tpl_vars['discussion']['thread_id'], $this->_tpl_vars['_REQUEST']['page']), false); ?>

<?php if ($this->_tpl_vars['posts']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array('id' => "pagination_contents_comments_".($this->_tpl_vars['object_id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_from = $this->_tpl_vars['posts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['post']):
?>
<div class="posts<?php echo smarty_function_cycle(array('values' => ", manage-post"), $this);?>
" id="post_<?php echo $this->_tpl_vars['post']['post_id']; ?>
">
<?php $this->_tag_stack[] = array('hook', array('name' => "discussion:items_list_row")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<div class="clear">
		<?php if ($this->_tpl_vars['discussion']['type'] == 'R' || $this->_tpl_vars['discussion']['type'] == 'B'): ?>
		<div class="float-left">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/discussion/views/discussion/components/stars.tpl", 'smarty_include_vars' => array('stars' => fn_get_discussion_rating($this->_tpl_vars['post']['rating_value']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<?php endif; ?>
		<div class="float-right">
			<em><?php echo smarty_modifier_date_format($this->_tpl_vars['post']['timestamp'], ($this->_tpl_vars['settings']['Appearance']['date_format']).", ".($this->_tpl_vars['settings']['Appearance']['time_format'])); ?>
</em>
		</div>
	</div>
	
	<?php if ($this->_tpl_vars['discussion']['type'] == 'C' || $this->_tpl_vars['discussion']['type'] == 'B'): ?><p class="post-message">"<?php echo smarty_modifier_nl2br(smarty_modifier_escape($this->_tpl_vars['post']['message'])); ?>
"</p><?php endif; ?>
	<p class="post-author">&ndash; <?php echo smarty_modifier_escape($this->_tpl_vars['post']['name']); ?>
</p>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
</div>
<?php endforeach; endif; unset($_from); ?>
<?php if ($this->_tpl_vars['object_type'] == 'E' && $this->_tpl_vars['current_post_id']): ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function()<?php echo $this->_tpl_vars['ldelim']; ?>

	if ($('#post_' + <?php echo $this->_tpl_vars['current_post_id']; ?>
).length) <?php echo $this->_tpl_vars['ldelim']; ?>

		jQuery.scrollToElm($('#post_' + <?php echo $this->_tpl_vars['current_post_id']; ?>
));
	<?php echo $this->_tpl_vars['rdelim']; ?>

<?php echo $this->_tpl_vars['rdelim']; ?>
);
//]]>
</script>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array('id' => "pagination_contents_comments_".($this->_tpl_vars['object_id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
<p class="no-items"><?php echo fn_get_lang_var('no_posts_found', $this->getLanguage()); ?>
</p>
<?php endif; ?>

<?php if (strpos('CRB', $this->_tpl_vars['discussion']['type']) !== false): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('new_post', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="add_post_form">
<input type ="hidden" name="post_data[thread_id]" value="<?php echo $this->_tpl_vars['discussion']['thread_id']; ?>
" />
<input type ="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
" />
<input type="hidden" name="selected_section" value="" />

<div class="form-field">
	<label for="dsc_name" class="cm-required"><?php echo fn_get_lang_var('your_name', $this->getLanguage()); ?>
:</label>
	<input type="text" id="dsc_name" name="post_data[name]" value="<?php if ($this->_tpl_vars['auth']['user_id']): ?><?php echo $this->_tpl_vars['user_info']['firstname']; ?>
 <?php echo $this->_tpl_vars['user_info']['lastname']; ?>
<?php elseif ($this->_tpl_vars['discussion']['post_data']['name']): ?><?php echo $this->_tpl_vars['discussion']['post_data']['name']; ?>
<?php endif; ?>" size="50" class="input-text" />
</div>

<?php if ($this->_tpl_vars['discussion']['type'] == 'R' || $this->_tpl_vars['discussion']['type'] == 'B'): ?>
<div class="form-field">
	<label for="dsc_rating" class="cm-required"><?php echo fn_get_lang_var('your_rating', $this->getLanguage()); ?>
:</label>
	<select id="dsc_rating" name="post_data[rating_value]">
		<option value="5" selected="selected"><?php echo fn_get_lang_var('excellent', $this->getLanguage()); ?>
</option>
		<option value="4" <?php if ($this->_tpl_vars['discussion']['post_data']['rating_value'] == '4'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('very_good', $this->getLanguage()); ?>
</option>
		<option value="3" <?php if ($this->_tpl_vars['discussion']['post_data']['rating_value'] == '3'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('average', $this->getLanguage()); ?>
</option>
		<option value="2" <?php if ($this->_tpl_vars['discussion']['post_data']['rating_value'] == '2'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('fair', $this->getLanguage()); ?>
</option>
		<option value="1" <?php if ($this->_tpl_vars['discussion']['post_data']['rating_value'] == '1'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('poor', $this->getLanguage()); ?>
</option>
	</select>
</div>
<?php endif; ?>

<?php $this->_tag_stack[] = array('hook', array('name' => "discussion:add_post")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php if ($this->_tpl_vars['discussion']['type'] == 'C' || $this->_tpl_vars['discussion']['type'] == 'B'): ?>
<div class="form-field">
	<label for="dsc_message" class="cm-required"><?php echo fn_get_lang_var('your_message', $this->getLanguage()); ?>
:</label>
	<textarea id="dsc_message" name="post_data[message]" class="input-textarea" rows="5" cols="72"><?php echo $this->_tpl_vars['discussion']['post_data']['message']; ?>
</textarea>
</div>
<?php endif; ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>


<?php if ($this->_tpl_vars['settings']['Image_verification']['use_for_discussion'] == 'Y'): ?>
	<?php endif; ?>
<h2 class="subheader">
		
Защита анти-спам.
</h2>
<img src="/images/capch.gif">
<br/>
<label>Введите код с картинки</label>
<input name="post_data[captcha_value]"/>

<div class="buttons-container">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('submit', $this->getLanguage()),'but_name' => "dispatch[discussion.add_post]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

</form>

<?php endif; ?>

<?php if ($this->_tpl_vars['wrap'] == true): ?>
	<?php $this->_smarty_vars['capture']['content'] = ob_get_contents(); ob_end_clean(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/group.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['content'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<?php ob_start(); ?><?php echo $this->_tpl_vars['title']; ?>
<?php $this->_smarty_vars['capture']['mainbox_title'] = ob_get_contents(); ob_end_clean(); ?>
<?php endif; ?>
</div>
<?php endif; ?>