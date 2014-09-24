<?php /* Smarty version 2.6.18, created on 2014-09-24 21:26:16
         compiled from views/countries/manage.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/countries/manage.tpl', 5, false),array('function', 'cycle', 'views/countries/manage.tpl', 19, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('code','code','code','country','region','status','delete_selected','countries'));
?>

<?php ob_start(); ?>

<form action="<?php echo fn_url(""); ?>
" method="post" name="countries_form">

<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table">
<tr>
	<th class="center"><?php echo fn_get_lang_var('code', $this->getLanguage()); ?>
</th>
	<th class="center"><?php echo fn_get_lang_var('code', $this->getLanguage()); ?>
&nbsp;A3</th>
	<th class="center"><?php echo fn_get_lang_var('code', $this->getLanguage()); ?>
&nbsp;N3</th>
	<th><?php echo fn_get_lang_var('country', $this->getLanguage()); ?>
</th>
	<th class="center"><?php echo fn_get_lang_var('region', $this->getLanguage()); ?>
</th>
	<th width="5%"><?php echo fn_get_lang_var('status', $this->getLanguage()); ?>
</th>
</tr>

<?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['country']):
?>
<tr <?php echo smarty_function_cycle(array('values' => "class=\"table-row\", "), $this);?>
>
	<td class="center">
		<?php echo $this->_tpl_vars['country']['code']; ?>
</td>
	<td class="center">
		<?php echo $this->_tpl_vars['country']['code_A3']; ?>
</td>
	<td class="center">
		<?php echo $this->_tpl_vars['country']['code_N3']; ?>
</td>
	<td>
		<input type="text" name="country_description[<?php echo $this->_tpl_vars['country']['code']; ?>
][country]" size="55" value="<?php echo $this->_tpl_vars['country']['country']; ?>
" class="input-text" /></td>
	<td class="center">
		<?php echo $this->_tpl_vars['country']['region']; ?>
</td>
	<td>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/select_popup.tpl", 'smarty_include_vars' => array('id' => $this->_tpl_vars['country']['code'],'status' => $this->_tpl_vars['country']['status'],'hidden' => "",'object_id_name' => 'code','table' => 'countries')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/pagination.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="buttons-container buttons-bg">
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/save.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[countries.update]",'but_role' => 'button_main')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
		
		<?php ob_start(); ?>
		<ul>
			<li><a name="dispatch[countries.delete]" class="cm-process-items cm-confirm" rev="countries_form"><?php echo fn_get_lang_var('delete_selected', $this->getLanguage()); ?>
</a></li>
		</ul>
		<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>
			
</div>

</form>

 
<?php $this->_smarty_vars['capture']['mainbox'] = ob_get_contents(); ob_end_clean(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/mainbox.tpl", 'smarty_include_vars' => array('title' => fn_get_lang_var('countries', $this->getLanguage()),'content' => $this->_smarty_vars['capture']['mainbox'],'select_languages' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>