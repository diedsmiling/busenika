<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:41
         compiled from bottom.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'defined', 'bottom.tpl', 3, false),array('modifier', 'fn_url', 'bottom.tpl', 11, false),array('modifier', 'default', 'bottom.tpl', 14, false),array('modifier', 'fn_check_meta_redirect', 'bottom.tpl', 46, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('quick_search','quick_search','product_code','order_id','user','search_in_content','search_product','or','choose','last_viewed_items','open_store','close_store'));
?>

<?php if (defined('DEBUG_MODE')): ?>
<div class="bug-report">
	<input type="button" onclick="window.open('bug_report.php','popupwindow','width=700,height=450,toolbar=yes,status=no,scrollbars=yes,resizable=no,menubar=yes,location=no,direction=no');" value="Report a bug" />
</div>
<?php endif; ?>

<div id="bottom_menu">
	<div class="float-left">
		<form id="bottom_quick_search" name="quick_search_form" action="<?php echo fn_url(""); ?>
">
			<input type="hidden" value="Y" name="redirect_if_one" />
			<input type="hidden" value="<?php echo fn_get_lang_var('quick_search', $this->getLanguage()); ?>
..." name="_default_search" id="elm_default_search" />
			<input type="text" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['search']['q'], (fn_get_lang_var('quick_search', $this->getLanguage()))."..."); ?>
" name="q" id="quick_search" class="input-text <?php if ($this->_tpl_vars['search']['q'] == ""): ?>cm-hint<?php endif; ?>" />
			<?php ob_start(); ?>
			<ul>
				<li><a name="dispatch[products.manage]" rev="bottom_quick_search" onmouseover="$('#quick_search').attr('name', 'pcode');"><?php echo fn_get_lang_var('product_code', $this->getLanguage()); ?>
</a></li>
				<li><a name="dispatch[orders.manage]" rev="bottom_quick_search" onmouseover="$('#quick_search').attr('name', 'order_id');"><?php echo fn_get_lang_var('order_id', $this->getLanguage()); ?>
</a></li>
				<li><a name="dispatch[profiles.manage]" rev="bottom_quick_search" onmouseover="$('#quick_search').attr('name', 'name');"><?php echo fn_get_lang_var('user', $this->getLanguage()); ?>
</a></li>
				<?php if ($this->_tpl_vars['settings']['General']['search_objects']): ?>
				<li><a name="dispatch[search.results]" rev="bottom_quick_search" onmouseover="$('#quick_search').attr('name', 'q');"><?php echo fn_get_lang_var('search_in_content', $this->getLanguage()); ?>
</a></li>
				<?php endif; ?>
			</ul>
			<?php $this->_smarty_vars['capture']['tools_list'] = ob_get_contents(); ob_end_clean(); ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('search_product', $this->getLanguage()),'but_name' => "dispatch[products.manage]",'but_onclick' => "$('#quick_search').attr('name', 'q').val($('#quick_search').val() == $('#elm_default_search').val() ? '' : $('#quick_search').val());",'but_role' => 'submit','allow_href' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <?php echo fn_get_lang_var('or', $this->getLanguage()); ?>
 <?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/tools.tpl", 'smarty_include_vars' => array('prefix' => 'bottom','hide_actions' => true,'tools_list' => $this->_smarty_vars['capture']['tools_list'],'display' => 'inline','link_text' => fn_get_lang_var('choose', $this->getLanguage()),'link_meta' => 'lowercase')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</form>
	</div>

	<div class="float-right">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/last_viewed_items.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<div id="bottom_popup_menu_wrap">
			<a id="sw_last_edited_items" class="cm-combo-on cm-combination"><?php echo fn_get_lang_var('last_viewed_items', $this->getLanguage()); ?>
</a>
		</div>
	</div>

	<div class="float-right" id="store_mode">
		<a href="/zenon.php?dispatch=exim.clear_cache">Очистить кэш</a>&nbsp;&nbsp;
		<?php if ($this->_tpl_vars['settings']['store_mode'] == 'closed'): ?>
			<a class="cm-ajax cm-confirm text-button" rev="store_mode" href="<?php echo fn_url("tools.store_mode?state=opened"); ?>
"><?php echo fn_get_lang_var('open_store', $this->getLanguage()); ?>
</a>
		<?php else: ?>
			<a class="cm-ajax cm-confirm text-button" rev="store_mode" href="<?php echo fn_url("tools.store_mode?state=closed"); ?>
"><?php echo fn_get_lang_var('close_store', $this->getLanguage()); ?>
</a>
		<?php endif; ?>
	<!--store_mode--></div>
</div>

<?php if (fn_check_meta_redirect($this->_tpl_vars['_REQUEST']['meta_redirect_url'])): ?>
	<meta http-equiv="refresh" content="1;url=<?php echo fn_url(fn_check_meta_redirect($this->_tpl_vars['_REQUEST']['meta_redirect_url'])); ?>
" />
<?php endif; ?>