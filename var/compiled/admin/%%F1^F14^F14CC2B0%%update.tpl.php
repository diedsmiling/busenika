<?php /* Smarty version 2.6.18, created on 2014-09-23 22:34:33
         compiled from views/profiles/update.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/profiles/update.tpl', 9, false),array('modifier', 'default', 'views/profiles/update.tpl', 10, false),array('modifier', 'fn_compare_shipping_billing', 'views/profiles/update.tpl', 37, false),array('modifier', 'defined', 'views/profiles/update.tpl', 43, false),array('modifier', 'fn_get_user_type_description', 'views/profiles/update.tpl', 103, false),array('modifier', 'fn_user_need_login', 'views/profiles/update.tpl', 115, false),array('block', 'hook', 'views/profiles/update.tpl', 15, false),array('function', 'cycle', 'views/profiles/update.tpl', 51, false),array('function', 'script', 'views/profiles/update.tpl', 123, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('vendor','contact_information','user_profile_info','text_multiprofile_notice','billing_address','shipping_address','shipping_address','usergroup','status','active','pending','available','declined','no_items','notify_user','new_profile','editing_profile','editing_profile','view_all_orders','act_on_behalf'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profiles_scripts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>

<?php ob_start(); ?>

	<form name="profile_form" action="<?php echo fn_url(""); ?>
" method="post" class="cm-form-highlight">
	<?php if ($this->_tpl_vars['mode'] != 'add'): ?><input type="hidden" name="user_id" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['_REQUEST']['user_id'], @$this->_tpl_vars['auth']['user_id']); ?>
" /><?php endif; ?>
	<input type="hidden" name="selected_section" id="selected_section" value="<?php echo $this->_tpl_vars['selected_section']; ?>
" />

	
	<div id="content_general">
		<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:general_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<fieldset>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profiles_account.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/companies/components/company_field.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('vendor', $this->getLanguage()),'name' => "user_data[company_id]",'selected' => $this->_tpl_vars['user_data']['company_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</fieldset>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		
		<fieldset>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profile_fields.tpl", 'smarty_include_vars' => array('section' => 'C','title' => fn_get_lang_var('contact_information', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</fieldset>

		<?php if ($this->_tpl_vars['settings']['General']['user_multiple_profiles'] == 'Y' && $this->_tpl_vars['mode'] == 'update'): ?>
		<fieldset>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('user_profile_info', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<p class="form-note"><?php echo fn_get_lang_var('text_multiprofile_notice', $this->getLanguage()); ?>
</p>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/multiple_profiles.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</fieldset>
		<?php endif; ?>

		<fieldset>
		<?php if ($this->_tpl_vars['profile_fields']['B']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profile_fields.tpl", 'smarty_include_vars' => array('section' => 'B','title' => fn_get_lang_var('billing_address', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profile_fields.tpl", 'smarty_include_vars' => array('section' => 'S','title' => fn_get_lang_var('shipping_address', $this->getLanguage()),'body_id' => 'sa','shipping_flag' => fn_compare_shipping_billing($this->_tpl_vars['profile_fields']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profile_fields.tpl", 'smarty_include_vars' => array('section' => 'S','title' => fn_get_lang_var('shipping_address', $this->getLanguage()),'shipping_flag' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
		</fieldset>
	</div>
	<?php if ($this->_tpl_vars['mode'] == 'update' && ( ( $this->_tpl_vars['user_data']['user_type'] != 'A' && ! defined('COMPANY_ID') ) || ( $this->_tpl_vars['user_data']['user_type'] == 'A' && ! defined('COMPANY_ID') && $this->_tpl_vars['auth']['is_root'] == 'Y' && ( $this->_tpl_vars['user_data']['company_id'] != 0 || ( $this->_tpl_vars['user_data']['company_id'] == 0 && $this->_tpl_vars['user_data']['is_root'] != 'Y' ) ) ) || ( $this->_tpl_vars['user_data']['user_type'] == 'A' && defined('COMPANY_ID') && $this->_tpl_vars['auth']['is_root'] == 'Y' && $this->_tpl_vars['user_data']['user_id'] != $this->_tpl_vars['auth']['user_id'] && $this->_tpl_vars['user_data']['company_id'] == @COMPANY_ID ) )): ?>
	<div id="content_usergroups" class="cm-hide-save-button">
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
		<tr>
			<th width="50%"><?php echo fn_get_lang_var('usergroup', $this->getLanguage()); ?>
</th>
			<th><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</th>
		</tr>
		<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
		<tr <?php echo smarty_function_cycle(array('values' => "class=\"table-row\", "), $this);?>
>
			<td><a href="<?php echo fn_url("usergroups.manage#group".($this->_tpl_vars['usergroup']['usergroup_id'])); ?>
"><?php echo $this->_tpl_vars['usergroup']['usergroup']; ?>
</a></td>
			<td>
				<?php if ($this->_tpl_vars['user_data']['usergroups'][$this->_tpl_vars['usergroup']['usergroup_id']]): ?>
					<?php $this->assign('ug_status', $this->_tpl_vars['user_data']['usergroups'][$this->_tpl_vars['usergroup']['usergroup_id']]['status'], false); ?>
				<?php else: ?>
					<?php $this->assign('ug_status', 'F', false); ?>
				<?php endif; ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_popup.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['usergroup']['usergroup_id'],'status' => $this->_tpl_vars['ug_status'],'hidden' => "",'items_status' => "A: ".(fn_get_lang_var('active', $this->getLanguage())).", P: ".(fn_get_lang_var('pending', $this->getLanguage())).", F: ".(fn_get_lang_var('available', $this->getLanguage())).", D: ".(fn_get_lang_var('declined', $this->getLanguage())),'extra' => "&amp;user_id=".($this->_tpl_vars['user_data']['user_id']),'update_controller' => 'usergroups','notify' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</td>
		</tr>
		<?php endforeach; else: ?>
		<tr class="no-items">
			<td colspan="2"><p><?php echo fn_get_lang_var('no_items', $this->getLanguage()); ?>
</p></td>
		</tr>
		<?php endif; unset($_from); ?>
		</table>
	</div>
	<?php endif; ?>
	<div id="content_addons">
		<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:detailed_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php if ($this->_tpl_vars['addons']['age_verification']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/age_verification/hooks/profiles/detailed_content.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	</div>

	<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:tabs_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

	<p class="select-field notify-customer cm-toggle-button">
		<input type="checkbox" name="notify_customer" value="Y" checked="checked" class="checkbox" id="notify_customer" />
		<label for="notify_customer"><?php echo fn_get_lang_var('notify_user', $this->getLanguage()); ?>
</label>
	</p>

	<div class="buttons-container buttons-bg cm-toggle-button">
		<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[profiles.add]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save_cancel.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[profiles.update.".($this->_tpl_vars['action'])."]")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
	</div>

	</form>

	<?php if ($this->_tpl_vars['mode'] != 'add'): ?>
		<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:tabs_extra")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
		<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']['tabsbox'] = ob_get_contents(); ob_end_clean(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tabsbox.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['tabsbox'],'group_name' => $this->_tpl_vars['controller'],'active_tab' => $this->_tpl_vars['selected_section'],'track' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php if ($this->_tpl_vars['mode'] == 'add'): ?>
	<?php $this->assign('_user_desc', fn_get_user_type_description($this->_tpl_vars['user_type']), false); ?>
	<?php $this->assign('_title', (fn_get_lang_var('new_profile', $this->getLanguage()))." (".($this->_tpl_vars['_user_desc']).")", false); ?>
<?php else: ?>
	<?php if ($this->_tpl_vars['user_data']['firstname']): ?>
		<?php $this->assign('_title', (fn_get_lang_var('editing_profile', $this->getLanguage())).": ".($this->_tpl_vars['user_data']['firstname'])." ".($this->_tpl_vars['user_data']['lastname']), false); ?>
	<?php else: ?>
		<?php $this->assign('_title', (fn_get_lang_var('editing_profile', $this->getLanguage())).": ".($this->_tpl_vars['user_data']['company']), false); ?>
	<?php endif; ?>
	<?php ob_start(); ?>
		<?php if ($this->_tpl_vars['user_data']['user_type'] == 'C'): ?>
			<a class="tool-link" href="<?php echo fn_url("orders.manage?user_id=".($this->_tpl_vars['user_data']['user_id'])); ?>
"><?php echo fn_get_lang_var('view_all_orders', $this->getLanguage()); ?>
</a>&nbsp;&nbsp;|&nbsp;
		<?php endif; ?>
		<?php if (fn_user_need_login($this->_tpl_vars['user_data']['user_type'])): ?>
			<a class="tool-link" href="<?php echo fn_url("profiles.act_as_user?user_id=".($this->_tpl_vars['user_data']['user_id'])); ?>
" target="_blank"><?php echo fn_get_lang_var('act_on_behalf', $this->getLanguage()); ?>
</a>
		<?php endif; ?>
	<?php $this->_smarty_vars['capture']['extra_tools'] = ob_get_contents(); ob_end_clean(); ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/view_tools.tpl", 'smarty_include_vars' => array('url' => "profiles.update?user_id=")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['_title'],'content' => $this->_smarty_vars['capture']['mainbox'],'extra_tools' => $this->_smarty_vars['capture']['extra_tools'],'tools' => $this->_smarty_vars['capture']['view_tools'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>