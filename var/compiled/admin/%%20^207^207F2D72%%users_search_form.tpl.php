<?php /* Smarty version 2.6.18, created on 2014-09-22 23:31:47
         compiled from views/profiles/components/users_search_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/profiles/components/users_search_form.tpl', 5, false),array('modifier', 'escape', 'views/profiles/components/users_search_form.tpl', 130, false),array('block', 'hook', 'views/profiles/components/users_search_form.tpl', 115, false),array('function', 'script', 'views/profiles/components/users_search_form.tpl', 136, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('name','company','email','username','usergroup','not_a_member','tax_exempt','yes','no','address','city','country','select_country','state','select_state','zip_postal_code','ordered_products'));
?>

<?php ob_start(); ?>

<form name="user_search_form" action="<?php echo fn_url(""); ?>
" method="get">

<?php if ($this->_tpl_vars['_REQUEST']['redirect_url']): ?>
<input type="hidden" name="redirect_url" value="<?php echo $this->_tpl_vars['_REQUEST']['redirect_url']; ?>
" />
<?php endif; ?>

<?php if ($this->_tpl_vars['selected_section'] != ""): ?>
<input type="hidden" id="selected_section" name="selected_section" value="<?php echo $this->_tpl_vars['selected_section']; ?>
" />
<?php endif; ?>

<?php if ($this->_tpl_vars['search']['user_type']): ?>
<input type="hidden" name="user_type" value="<?php echo $this->_tpl_vars['search']['user_type']; ?>
" />
<?php endif; ?>

<?php echo $this->_tpl_vars['extra']; ?>


<table cellpadding="0" cellspacing="0" border="0" class="search-header">
<tr>
	<td class="search-field nowrap">
		<label for="elm_name"><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
:</label>
		<div class="break">
			<input class="search-input-text" type="text" name="name" id="elm_name" value="<?php echo $this->_tpl_vars['search']['name']; ?>
" />
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/search_go.tpl", 'smarty_include_vars' => array('search' => 'Y','but_name' => $this->_tpl_vars['dispatch'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</td>
	<td class="search-field">
		<label for="elm_company"><?php echo fn_get_lang_var('company', $this->getLanguage()); ?>
:</label>
		<div class="break">
			<input class="input-text" type="text" name="company" id="elm_company" value="<?php echo $this->_tpl_vars['search']['company']; ?>
" />
		</div>
	</td>
	<td class="search-field">
		<label for="elm_email"><?php echo fn_get_lang_var('email', $this->getLanguage()); ?>
:</label>
		<div class="break">
			<input class="input-text" type="text" name="email" id="elm_email" value="<?php echo $this->_tpl_vars['search']['email']; ?>
" />
		</div>
	</td>
	<td class="buttons-container">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/search.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[".($this->_tpl_vars['dispatch'])."]",'but_role' => 'submit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
</table>

<?php ob_start(); ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr valign="top">
	<td>
		<?php if ($this->_tpl_vars['settings']['General']['use_email_as_login'] != 'Y'): ?>
		<div class="search-field">
			<label for="elm_user_login"><?php echo fn_get_lang_var('username', $this->getLanguage()); ?>
:</label>
			<input class="input-text" type="text" name="user_login" id="elm_user_login" value="<?php echo $this->_tpl_vars['search']['user_login']; ?>
" />
		</div>
		<?php endif; ?>
		<div class="search-field">
			<label for="elm_usergroup_id"><?php echo fn_get_lang_var('usergroup', $this->getLanguage()); ?>
:</label>
			<select name="usergroup_id" id="elm_usergroup_id">
				<option value="<?php echo @ALL_USERGROUPS; ?>
"> -- </option>
				<option value="0" <?php if ($this->_tpl_vars['search']['usergroup_id'] === '0'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('not_a_member', $this->getLanguage()); ?>
</option>
				<?php $_from = $this->_tpl_vars['usergroups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['usergroup']):
?>
				<option value="<?php echo $this->_tpl_vars['usergroup']['usergroup_id']; ?>
" <?php if ($this->_tpl_vars['search']['usergroup_id'] == $this->_tpl_vars['usergroup']['usergroup_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['usergroup']['usergroup']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		</div>
		<div class="search-field">
			<label for="elm_tax_exempt"><?php echo fn_get_lang_var('tax_exempt', $this->getLanguage()); ?>
:</label>
			<select name="tax_exempt" id="elm_tax_exempt">
				<option value="">--</option>
				<option value="Y" <?php if ($this->_tpl_vars['search']['tax_exempt'] == 'Y'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('yes', $this->getLanguage()); ?>
</option>
				<option value="N" <?php if ($this->_tpl_vars['search']['tax_exempt'] == 'N'): ?>selected="selected"<?php endif; ?>><?php echo fn_get_lang_var('no', $this->getLanguage()); ?>
</option>
			</select>
		</div>

		<div class="search-field">
			<label for="elm_address"><?php echo fn_get_lang_var('address', $this->getLanguage()); ?>
:</label>
			<input class="input-text" type="text" name="address" id="elm_address" value="<?php echo $this->_tpl_vars['search']['address']; ?>
" />
		</div>
	</td>
	<td>

		<div class="search-field">
			<label for="elm_city"><?php echo fn_get_lang_var('city', $this->getLanguage()); ?>
:</label>
			<input class="input-text" type="text" name="city" id="elm_city" value="<?php echo $this->_tpl_vars['search']['city']; ?>
" />
		</div>
		<div class="search-field">
			<label for="srch_country" class="cm-country cm-location-search"><?php echo fn_get_lang_var('country', $this->getLanguage()); ?>
:</label>
			<select id="srch_country" name="country" class="cm-location-search">
				<option value="">- <?php echo fn_get_lang_var('select_country', $this->getLanguage()); ?>
 -</option>
				<?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['country']):
?>
				<option value="<?php echo $this->_tpl_vars['country']['code']; ?>
" <?php if ($this->_tpl_vars['search']['country'] == $this->_tpl_vars['country']['code']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['country']['country']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		</div>

		<div class="search-field">
			<label for="srch_state" class="cm-state cm-location-search"><?php echo fn_get_lang_var('state', $this->getLanguage()); ?>
:</label>
			<input type="text" id="srch_state_d" name="state" maxlength="64" value="<?php echo $this->_tpl_vars['search']['state']; ?>
" disabled="disabled" class="input-text" />
			<select id="srch_state" class="hidden" name="state">
				<option value="">- <?php echo fn_get_lang_var('select_state', $this->getLanguage()); ?>
 -</option>
			</select>
		</div>

		<div class="search-field">
			<label for="elm_zipcode"><?php echo fn_get_lang_var('zip_postal_code', $this->getLanguage()); ?>
:</label>
			<input class="input-text" type="text" name="zipcode" id="elm_zipcode" value="<?php echo $this->_tpl_vars['search']['zipcode']; ?>
" />
		</div>
	</td>
</tr>
</table>

<?php $this->_tag_stack[] = array('hook', array('name' => "profiles:search_form")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<div class="search-field">
	<label><?php echo fn_get_lang_var('ordered_products', $this->getLanguage()); ?>
:</label>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "pickers/search_products_picker.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<?php $this->_smarty_vars['capture']['advanced_search'] = ob_get_contents(); ob_end_clean(); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/advanced_search.tpl", 'smarty_include_vars' => array('content' => $this->_smarty_vars['capture']['advanced_search'],'dispatch' => $this->_tpl_vars['dispatch'],'view_type' => 'users')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</form>
<script type="text/javascript">
//<![CDATA[
	default_state = <?php echo $this->_tpl_vars['ldelim']; ?>
'search':'<?php echo smarty_modifier_escape($this->_tpl_vars['_REQUEST']['state'], 'javascript'); ?>
'<?php echo $this->_tpl_vars['rdelim']; ?>
;
//]]>
</script>

<?php $this->_smarty_vars['capture']['section'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/section.tpl", 'smarty_include_vars' => array('section_content' => $this->_smarty_vars['capture']['section'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php echo smarty_function_script(array('src' => "js/jquery.simpletip-1.3.1.js"), $this);?>