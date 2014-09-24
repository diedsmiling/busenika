<?php /* Smarty version 2.6.18, created on 2014-09-24 21:48:48
         compiled from views/profiles/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/profiles/update.tpl', 8, false),array('block', 'hook', 'views/profiles/update.tpl', 40, false),array('function', 'cycle', 'views/profiles/update.tpl', 81, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('text_mandatory_fields','contact_information','text_multiprofile_notice','billing_address','shipping_address','shipping_address','billing_address','usergroup','status','action','active','remove','available','join','declined','join','pending','cancel','profile_details'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profiles_scripts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>
	<div id="content_general">
		<?php echo fn_get_lang_var('text_mandatory_fields', $this->getLanguage()); ?>

		<form name="profile_form" action="<?php echo fn_url(""); ?>
" method="post">
		<input id="selected_section" type="hidden" value="general" name="selected_section"/>
		<input id="default_card_id" type="hidden" value="" name="default_cc"/>
		<input type="hidden" name="profile_id" value="<?php echo $this->_tpl_vars['user_data']['profile_id']; ?>
" />
		<?php ob_start(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profiles_account.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profile_fields.tpl", 'smarty_include_vars' => array('section' => 'C','title' => fn_get_lang_var('contact_information', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<?php if ($this->_tpl_vars['profile_fields']['B'] || $this->_tpl_vars['profile_fields']['S']): ?>
			<?php if ($this->_tpl_vars['settings']['General']['user_multiple_profiles'] == 'Y' && $this->_tpl_vars['mode'] == 'update'): ?>
				<p><?php echo fn_get_lang_var('text_multiprofile_notice', $this->getLanguage()); ?>
</p>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/multiple_profiles.tpl", 'smarty_include_vars' => array('profile_id' => $this->_tpl_vars['user_data']['profile_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>	
			<?php endif; ?>
			
			<?php if ($this->_tpl_vars['settings']['General']['address_position'] == 'billing_first'): ?>
				<?php $this->assign('first_section', 'B', false); ?>
				<?php $this->assign('first_section_text', fn_get_lang_var('billing_address', $this->getLanguage()), false); ?>
				<?php $this->assign('sec_section', 'S', false); ?>
				<?php $this->assign('sec_section_text', fn_get_lang_var('shipping_address', $this->getLanguage()), false); ?>
				<?php $this->assign('body_id', 'sa', false); ?>
			<?php else: ?>
				<?php $this->assign('first_section', 'S', false); ?>
				<?php $this->assign('first_section_text', fn_get_lang_var('shipping_address', $this->getLanguage()), false); ?>
				<?php $this->assign('sec_section', 'B', false); ?>
				<?php $this->assign('sec_section_text', fn_get_lang_var('billing_address', $this->getLanguage()), false); ?>
				<?php $this->assign('body_id', 'ba', false); ?>
			<?php endif; ?>
			
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profile_fields.tpl", 'smarty_include_vars' => array('section' => $this->_tpl_vars['first_section'],'body_id' => "",'ship_to_another' => 'Y','title' => $this->_tpl_vars['first_section_text'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profile_fields.tpl", 'smarty_include_vars' => array('section' => $this->_tpl_vars['sec_section'],'body_id' => $this->_tpl_vars['body_id'],'ship_to_another' => $this->_tpl_vars['ship_to_another'],'title' => $this->_tpl_vars['sec_section_text'],'address_flag' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

		<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:account_update")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?><?php if ($this->_tpl_vars['addons']['news_and_emails']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/news_and_emails/hooks/profiles/account_update.pre.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

		<?php if ($this->_tpl_vars['mode'] == 'add' && $this->_tpl_vars['settings']['Image_verification']['use_for_register'] == 'Y'): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/image_verification.tpl", 'smarty_include_vars' => array('id' => 'register','align' => 'center')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>

		<?php $this->_smarty_vars['capture']['group'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/group.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['group'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<div class="buttons-container center">
			<?php if ($this->_tpl_vars['action']): ?>
				<?php $this->assign('_action', ($this->_tpl_vars['action']), false); ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['mode'] == 'update'): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[profiles.update.".($this->_tpl_vars['_action'])."]",'but_id' => 'save_profile_but')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php else: ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/register_profile.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[profiles.add.".($this->_tpl_vars['_action'])."]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
		</div>
		</form>
	</div>
	
	<?php if ($this->_tpl_vars['mode'] == 'update'): ?>
	<?php if ($this->_tpl_vars['usergroups'] && $this->_tpl_vars['user_data']['user_type'] != 'A'): ?>
	<div id="content_usergroups">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table">
		<tr>
			<th width="30%"><?php echo fn_get_lang_var('usergroup', $this->getLanguage()); ?>
</th>
			<th width="30%"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</th>
			<?php if ($this->_tpl_vars['settings']['General']['allow_usergroup_signup'] == 'Y'): ?>
			<th width="40%"><?php echo fn_get_lang_var('action', $this->getLanguage()); ?>
</th>
			<?php endif; ?>
		</tr>
		<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
		<?php if ($this->_tpl_vars['user_data']['usergroups'][$this->_tpl_vars['usergroup']['usergroup_id']]): ?>
			<?php $this->assign('ug_status', $this->_tpl_vars['user_data']['usergroups'][$this->_tpl_vars['usergroup']['usergroup_id']]['status'], false); ?>
		<?php else: ?>
			<?php $this->assign('ug_status', 'F', false); ?>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['settings']['General']['allow_usergroup_signup'] == 'Y' || $this->_tpl_vars['settings']['General']['allow_usergroup_signup'] != 'Y' && $this->_tpl_vars['ug_status'] == 'A'): ?>
		<tr <?php echo smarty_function_cycle(array('values' => ",class=\"table-row\""), $this);?>
>
			<td><?php echo $this->_tpl_vars['usergroup']['usergroup']; ?>
</td>
			<td class="center">
				<?php if ($this->_tpl_vars['ug_status'] == 'A'): ?>
					<?php echo fn_get_lang_var('active', $this->getLanguage()); ?>

					<?php $this->assign('_link_text', fn_get_lang_var('remove', $this->getLanguage()), false); ?>
				<?php elseif ($this->_tpl_vars['ug_status'] == 'F'): ?>
					<?php echo fn_get_lang_var('available', $this->getLanguage()); ?>

					<?php $this->assign('_link_text', fn_get_lang_var('join', $this->getLanguage()), false); ?>
				<?php elseif ($this->_tpl_vars['ug_status'] == 'D'): ?>
					<?php echo fn_get_lang_var('declined', $this->getLanguage()); ?>

					<?php $this->assign('_link_text', fn_get_lang_var('join', $this->getLanguage()), false); ?>
				<?php elseif ($this->_tpl_vars['ug_status'] == 'P'): ?>
					<?php echo fn_get_lang_var('pending', $this->getLanguage()); ?>

					<?php $this->assign('_link_text', fn_get_lang_var('cancel', $this->getLanguage()), false); ?>
				<?php endif; ?>
			</td>
			<?php if ($this->_tpl_vars['settings']['General']['allow_usergroup_signup'] == 'Y'): ?>
			<td>
				<a class="cm-ajax" rev="content_usergroups" href="<?php echo fn_url("profiles.request_usergroup?usergroup_id=".($this->_tpl_vars['usergroup']['usergroup_id'])."&amp;status=".($this->_tpl_vars['ug_status'])); ?>
"><?php echo $this->_tpl_vars['_link_text']; ?>
</a>
			</td>
			<?php endif; ?>
		</tr>
		<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		<tr class="table-footer">
			<td colspan="<?php if ($this->_tpl_vars['settings']['General']['allow_usergroup_signup'] == 'Y'): ?>3<?php else: ?>2<?php endif; ?>">&nbsp;</td>
		</tr>
		</table>
	<!--content_usergroups--></div>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['settings']['General']['user_store_cc'] == 'Y'): ?>
	<div id="content_credit_cards">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/credit_cards.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']['tabsbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tabsbox.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['tabsbox'],'active_tab' => $this->_tpl_vars['_REQUEST']['selected_section'],'track' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?><?php echo fn_get_lang_var('profile_details', $this->getLanguage()); ?>
<?php $this->_smarty_vars['capture']['mainbox_title'] = ob_get_contents(); ob_end_clean(); ?>