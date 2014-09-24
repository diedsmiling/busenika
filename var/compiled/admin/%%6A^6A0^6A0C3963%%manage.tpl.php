<?php /* Smarty version 2.6.18, created on 2014-09-23 22:33:34
         compiled from views/profiles/manage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'views/profiles/manage.tpl', 10, false),array('modifier', 'fn_url', 'views/profiles/manage.tpl', 15, false),array('modifier', 'fn_query_remove', 'views/profiles/manage.tpl', 21, false),array('modifier', 'escape', 'views/profiles/manage.tpl', 52, false),array('modifier', 'date_format', 'views/profiles/manage.tpl', 53, false),array('modifier', 'fn_get_user_type_description', 'views/profiles/manage.tpl', 142, false),array('function', 'cycle', 'views/profiles/manage.tpl', 44, false),array('block', 'hook', 'views/profiles/manage.tpl', 66, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('text_list_of_user_accounts','text_list_of_all_accounts','check_uncheck_all','id','username','name','email','registered','type','status','administrator','customer','affiliate','notify_user','view_all_orders','act_on_behalf','delete','no_data','export_selected','delete_selected','choose_action','add_user','add_supplier','add_user','add_supplier','users'));
?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/profiles_scripts.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php ob_start(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/profiles/components/users_search_form.tpl", 'smarty_include_vars' => array('dispatch' => "profiles.manage")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['user_type_description']): ?>
<?php echo smarty_modifier_replace(fn_get_lang_var('text_list_of_user_accounts', $this->getLanguage()), "[account]", $this->_tpl_vars['user_type_description']); ?>

<?php else: ?>
<?php echo fn_get_lang_var('text_list_of_all_accounts', $this->getLanguage()); ?>

<?php endif; ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="userlist_form" id="userlist_form">
<input type="hidden" name="fake" value="1" />
<input type="hidden" name="user_type" value="<?php echo $this->_tpl_vars['_REQUEST']['user_type']; ?>
" />

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array('save_current_page' => true,'save_current_url' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $this->assign('c_url', fn_query_remove($this->_tpl_vars['config']['current_url'], 'sort_by', 'sort_order'), false); ?>

<?php if ($this->_tpl_vars['settings']['DHTML']['admin_ajax_based_pagination'] == 'Y'): ?>
	<?php $this->assign('ajax_class', "cm-ajax", false); ?>

<?php endif; ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table sortable">
<tr>
	<th width="1%" class="center">
		<input type="checkbox" name="check_all" value="Y" title="<?php echo fn_get_lang_var('check_uncheck_all', $this->getLanguage()); ?>
" class="checkbox cm-check-items" /></th>
	<th><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'id'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=id&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('id', $this->getLanguage()); ?>
</a></th>
	<?php if ($this->_tpl_vars['settings']['General']['use_email_as_login'] != 'Y'): ?>
	<th width="25%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'username'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=username&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('username', $this->getLanguage()); ?>
</a></th>
	<?php endif; ?>
	<th width="25%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'name'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=name&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
</a></th>
	<th width="25%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'email'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=email&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('email', $this->getLanguage()); ?>
</a></th>
	<th width="20%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'date'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=date&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('registered', $this->getLanguage()); ?>
</a></th>
	<th><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'type'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=type&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('type', $this->getLanguage()); ?>
</a></th>
	<th width="5%"><a class="<?php echo $this->_tpl_vars['ajax_class']; ?>
<?php if ($this->_tpl_vars['search']['sort_by'] == 'status'): ?> sort-link-<?php echo $this->_tpl_vars['search']['sort_order']; ?>
<?php endif; ?>" href="<?php echo fn_url(($this->_tpl_vars['c_url'])."&amp;sort_by=status&amp;sort_order=".($this->_tpl_vars['search']['sort_order'])); ?>
" rev="pagination_contents"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</a></th>
	<th>&nbsp;</th>
</tr>
<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
<tr <?php echo smarty_function_cycle(array('values' => "class=\"table-row\", "), $this);?>
>
	<td class="center">
		<input type="checkbox" name="user_ids[]" value="<?php echo $this->_tpl_vars['user']['user_id']; ?>
" class="checkbox cm-item" /></td>
	<td><a href="<?php echo fn_url("profiles.update?user_id=".($this->_tpl_vars['user']['user_id'])); ?>
">&nbsp;<strong><?php echo $this->_tpl_vars['user']['user_id']; ?>
</strong>&nbsp;</a></td>
	<?php if ($this->_tpl_vars['settings']['General']['use_email_as_login'] != 'Y'): ?>
	<td><a href="<?php echo fn_url("profiles.update?user_id=".($this->_tpl_vars['user']['user_id'])); ?>
"><?php echo $this->_tpl_vars['user']['user_login']; ?>
</a></td>
	<?php endif; ?>
	<td><?php if ($this->_tpl_vars['user']['firstname'] || $this->_tpl_vars['user']['lastname']): ?><a href="<?php echo fn_url("profiles.update?user_id=".($this->_tpl_vars['user']['user_id'])); ?>
"><?php echo $this->_tpl_vars['user']['lastname']; ?>
 <?php echo $this->_tpl_vars['user']['firstname']; ?>
</a><?php else: ?>-<?php endif; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/companies/components/company_name.tpl", 'smarty_include_vars' => array('company_id' => $this->_tpl_vars['user']['company_id'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
	<td width="25%"><a href="mailto:<?php echo smarty_modifier_escape($this->_tpl_vars['user']['email'], 'url'); ?>
"><?php echo $this->_tpl_vars['user']['email']; ?>
</a></td>
	<td><?php echo smarty_modifier_date_format($this->_tpl_vars['user']['timestamp'], ($this->_tpl_vars['settings']['Appearance']['date_format']).", ".($this->_tpl_vars['settings']['Appearance']['time_format'])); ?>
</td>
	<td><?php if ($this->_tpl_vars['user']['user_type'] == 'A'): ?><?php echo fn_get_lang_var('administrator', $this->getLanguage()); ?>
<?php elseif ($this->_tpl_vars['user']['user_type'] == 'C'): ?><?php echo fn_get_lang_var('customer', $this->getLanguage()); ?>
<?php elseif ($this->_tpl_vars['user']['user_type'] == 'P'): ?><?php echo fn_get_lang_var('affiliate', $this->getLanguage()); ?>
<?php endif; ?></td>
	<td>
		<input type="hidden" name="user_types[<?php echo $this->_tpl_vars['user']['user_id']; ?>
]" value="<?php echo $this->_tpl_vars['user']['user_type']; ?>
" />
		<?php if ($this->_tpl_vars['user']['user_id'] != 1): ?>
			<?php $this->assign('u_id', $this->_tpl_vars['user']['user_id'], false); ?>
		<?php else: ?>
			<?php $this->assign('u_id', "", false); ?>
		<?php endif; ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_popup.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['u_id'],'status' => $this->_tpl_vars['user']['status'],'hidden' => "",'update_controller' => 'profiles','notify' => true,'notify_text' => fn_get_lang_var('notify_user', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
	<td class="nowrap">
		<?php ob_start(); ?>
		<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:list_extra_links")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
			<?php if ($this->_tpl_vars['user']['user_type'] == 'C'): ?>
				<li><a href="<?php echo fn_url("orders.manage?user_id=".($this->_tpl_vars['user']['user_id'])); ?>
"><?php echo fn_get_lang_var('view_all_orders', $this->getLanguage()); ?>
</a></li>
				<li><a href="<?php echo fn_url("profiles.act_as_user?user_id=".($this->_tpl_vars['user']['user_id'])); ?>
" target="_blank" ><?php echo fn_get_lang_var('act_on_behalf', $this->getLanguage()); ?>
</a></li>
			<?php endif; ?>
			<?php $this->assign('return_current_url', smarty_modifier_escape($this->_tpl_vars['config']['current_url'], 'url'), false); ?>
			<li><a class="cm-confirm" href="<?php echo fn_url("profiles.delete?user_id=".($this->_tpl_vars['user']['user_id'])."&amp;redirect_url=".($this->_tpl_vars['return_current_url'])); ?>
"><?php echo fn_get_lang_var('delete', $this->getLanguage()); ?>
</a></li>
		<?php if ($this->_tpl_vars['addons']['affiliate']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/affiliate/hooks/profiles/list_extra_links.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php if ($this->_tpl_vars['addons']['reward_points']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/reward_points/hooks/profiles/list_extra_links.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		<?php $this->_smarty_vars['capture']['tools_items'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/table_tools_list.tpl", 'smarty_include_vars' => array('prefix' => $this->_tpl_vars['user']['user_id'],'tools_list' => $this->_smarty_vars['capture']['tools_items'],'href' => "profiles.update?user_id=".($this->_tpl_vars['user']['user_id']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
<?php endforeach; else: ?>
<tr class="no-items">
	<td colspan="9"><p><?php echo fn_get_lang_var('no_data', $this->getLanguage()); ?>
</p></td>
</tr>
<?php endif; unset($_from); ?>
</table>

<?php if ($this->_tpl_vars['users']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/table_tools.tpl", 'smarty_include_vars' => array('href' => "#users")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="buttons-container buttons-bg">
	<?php if ($this->_tpl_vars['users']): ?>
	<div class="float-left">
		<?php ob_start(); ?>
		<ul>
			<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:list_tools")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<li><a class="cm-process-items" name="dispatch[profiles.export_range]" rev="userlist_form"><?php echo fn_get_lang_var('export_selected', $this->getLanguage()); ?>
</a></li>
			<?php if ($this->_tpl_vars['addons']['myob']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/myob/hooks/profiles/list_tools.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
		</ul>
		<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('delete_selected', $this->getLanguage()),'but_name' => "dispatch[profiles.m_delete]",'but_meta' => "cm-confirm cm-process-items",'but_role' => 'button_main')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('prefix' => 'main','hide_actions' => true,'tools_list' => $this->_smarty_vars['capture']['tools_list'],'display' => 'inline','link_text' => fn_get_lang_var('choose_action', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	<?php endif; ?>
	
	<div class="float-right">
	<?php if ($this->_tpl_vars['_REQUEST']['user_type']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "profiles.add?user_type=".($this->_tpl_vars['_REQUEST']['user_type']),'prefix' => 'bottom','hide_tools' => true,'link_text' => fn_get_lang_var('add_user', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
		<?php if ($this->_tpl_vars['settings']['Suppliers']['enable_suppliers'] == 'Y'): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "companies.add",'prefix' => 'bottom','hide_tools' => true,'link_text' => fn_get_lang_var('add_supplier', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endif; ?>
		<?php $_from = $this->_tpl_vars['user_types']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['_k'] => $this->_tpl_vars['_p']):
?>
						<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "profiles.add?user_type=".($this->_tpl_vars['_k']),'prefix' => 'bottom','hide_tools' => true,'link_text' => fn_get_lang_var($this->_tpl_vars['_p'], $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
	</div>
</div>

<?php ob_start(); ?>
	<?php if ($this->_tpl_vars['_REQUEST']['user_type']): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "profiles.add?user_type=".($this->_tpl_vars['_REQUEST']['user_type']),'prefix' => 'top','hide_tools' => true,'link_text' => fn_get_lang_var('add_user', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php else: ?>
	<?php if ($this->_tpl_vars['settings']['Suppliers']['enable_suppliers'] == 'Y'): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "companies.add",'prefix' => 'bottom','hide_tools' => true,'link_text' => fn_get_lang_var('add_supplier', $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
	<?php $_from = $this->_tpl_vars['user_types']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['_k'] => $this->_tpl_vars['_p']):
?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('tool_href' => "profiles.add?user_type=".($this->_tpl_vars['_k']),'prefix' => 'top','hide_tools' => true,'link_text' => fn_get_lang_var($this->_tpl_vars['_p'], $this->getLanguage()))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endforeach; endif; unset($_from); ?>
	<?php endif; ?>
<?php $this->_smarty_vars['capture']['tools'] = ob_get_contents(); ob_end_clean(); ?>

</form>

<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>

<?php if ($this->_tpl_vars['_REQUEST']['user_type']): ?>
	<?php $this->assign('_title', fn_get_user_type_description($this->_tpl_vars['_REQUEST']['user_type'], true), false); ?>
<?php else: ?>
	<?php $this->assign('_title', fn_get_lang_var('users', $this->getLanguage()), false); ?>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['_title'],'content' => $this->_smarty_vars['capture']['mainbox'],'title_extra' => $this->_smarty_vars['capture']['title_extra'],'tools' => $this->_smarty_vars['capture']['tools'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>