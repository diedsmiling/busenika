<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:12
         compiled from addons/polls/blocks/sidebox.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'addons/polls/blocks/sidebox.tpl', 19, false),array('modifier', 'fn_url', 'addons/polls/blocks/sidebox.tpl', 19, false),array('modifier', 'unescape', 'addons/polls/blocks/sidebox.tpl', 23, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('polls_have_completed','view_results','submit'));
?>

<div class="wysiwyg-content">
<!--dynamic:polls-->
<?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['poll']):
?>
<?php if ($this->_tpl_vars['_REQUEST']['page_id'] != $this->_tpl_vars['poll']['page_id']): ?>

<?php if ($this->_tpl_vars['poll']['completed']): ?>
	<h4><?php echo fn_get_lang_var('polls_have_completed', $this->getLanguage()); ?>
</h4>
	<?php if ($this->_tpl_vars['poll']['show_results'] == 'Y'): ?>
	<div class="polls-buttons">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('view_results', $this->getLanguage()),'but_href' => "pages.view?page_id=".($this->_tpl_vars['poll']['page_id']),'but_role' => 'text')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>

<?php else: ?>

	<form name="<?php echo smarty_modifier_default(@$this->_tpl_vars['form_name'], 'main_login_form'); ?>
" action="<?php echo fn_url(""); ?>
" method="post">
	<input type="hidden" name="page_id" value="<?php echo $this->_tpl_vars['poll']['page_id']; ?>
" />
	<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['config']['current_url']; ?>
" />
	
	<?php if ($this->_tpl_vars['poll']['header']): ?><p><?php echo smarty_modifier_unescape($this->_tpl_vars['poll']['header']); ?>
</p><?php endif; ?>

	<?php if ($this->_tpl_vars['poll']['questions']): ?>
	<?php $_from = $this->_tpl_vars['poll']['questions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['question']):
?>
	<h4><?php echo $this->_tpl_vars['question']['description']; ?>
<?php if ($this->_tpl_vars['question']['required'] == 'Y'): ?>&nbsp;<span class="required-question">*</span><?php endif; ?></h4>
	<?php if ($this->_tpl_vars['question']['type'] == 'T'): ?>
	<textarea name="answer_text[<?php echo $this->_tpl_vars['question']['item_id']; ?>
]" class="poll-text-answer input-textarea"></textarea>
	<?php else: ?>
	<ul class="poll">
		<?php $_from = $this->_tpl_vars['question']['answers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['answer']):
?>
			<li>
				<?php if ($this->_tpl_vars['question']['type'] == 'Q'): ?>
					<input type="radio" class="radio" id="var_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['answer']['item_id']; ?>
" name="answer[<?php echo $this->_tpl_vars['question']['item_id']; ?>
]" value="<?php echo $this->_tpl_vars['answer']['item_id']; ?>
" />
				<?php else: ?>
					<input type="checkbox" id="var_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['answer']['item_id']; ?>
" name="answer[<?php echo $this->_tpl_vars['question']['item_id']; ?>
][<?php echo $this->_tpl_vars['answer']['item_id']; ?>
]" value="Y" />
				<?php endif; ?>
				<label for="var_<?php echo $this->_tpl_vars['block']['block_id']; ?>
_<?php echo $this->_tpl_vars['answer']['item_id']; ?>
"><?php echo $this->_tpl_vars['answer']['description']; ?>
</label>
				<?php if ($this->_tpl_vars['answer']['type'] == 'O'): ?>
					<p><input type="text" name="answer_more[<?php echo $this->_tpl_vars['question']['item_id']; ?>
][<?php echo $this->_tpl_vars['answer']['item_id']; ?>
]" class="input-text" value="" /></p>
				<?php endif; ?>
			</li>
		<?php endforeach; endif; unset($_from); ?>
	</ul>
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	
	<?php if ($this->_tpl_vars['poll']['footer']): ?><p><?php echo smarty_modifier_unescape($this->_tpl_vars['poll']['footer']); ?>
</p><?php endif; ?>

	<div class="image-verification">
		<?php if ($this->_tpl_vars['settings']['Image_verification']['use_for_polls'] == 'Y'): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image_verification.tpl", 'smarty_include_vars' => array('id' => "poll_".($this->_tpl_vars['block']['block_id'])."_".($this->_tpl_vars['poll']['page_id']),'sidebox' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

		<div class="polls-buttons">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('submit', $this->getLanguage()),'but_name' => "dispatch[pages.poll_submit]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>
	</form>

<?php endif; ?>

<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<!--/dynamic-->
</div>