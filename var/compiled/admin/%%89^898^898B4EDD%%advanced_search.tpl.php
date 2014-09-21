<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:41
         compiled from common_templates/advanced_search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_crc32', 'common_templates/advanced_search.tpl', 3, false),array('modifier', 'string_format', 'common_templates/advanced_search.tpl', 3, false),array('modifier', 'fn_get_views', 'common_templates/advanced_search.tpl', 13, false),array('modifier', 'strpos', 'common_templates/advanced_search.tpl', 22, false),array('modifier', 'escape', 'common_templates/advanced_search.tpl', 25, false),array('modifier', 'count', 'common_templates/advanced_search.tpl', 42, false),array('modifier', 'fn_url', 'common_templates/advanced_search.tpl', 52, false),array('modifier', 'fn_query_remove', 'common_templates/advanced_search.tpl', 60, false),array('function', 'split', 'common_templates/advanced_search.tpl', 35, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('advanced_search_options','save_this_search_as','name','save','all','all','delete','new_saved_search','new_saved_search','object_exists'));
?>

<?php $this->assign('a_id', smarty_modifier_string_format(fn_crc32($this->_tpl_vars['dispatch']), "s_%s"), false); ?>

<div class="advanced-search-button">
	<a id="sw_<?php echo $this->_tpl_vars['a_id']; ?>
" class="cm-combination cm-save-state">
		<?php echo fn_get_lang_var('advanced_search_options', $this->getLanguage()); ?>
&nbsp;
		<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/section_collapsed.gif" width="7" height="9" border="0" alt="" id="on_<?php echo $this->_tpl_vars['a_id']; ?>
" class="cm-combination cm-save-state <?php if ($_COOKIE[$this->_tpl_vars['a_id']]): ?>hidden<?php endif; ?>" />
		<img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/section_expanded.gif" width="7" height="9" border="0" alt="" id="off_<?php echo $this->_tpl_vars['a_id']; ?>
" class="cm-combination cm-save-state <?php if (! $_COOKIE[$this->_tpl_vars['a_id']]): ?>hidden<?php endif; ?>" />
	</a>
</div>

<?php $this->assign('views', fn_get_views($this->_tpl_vars['view_type']), false); ?>

<div id="<?php echo $this->_tpl_vars['a_id']; ?>
" class="search-advanced <?php if (! $_COOKIE[$this->_tpl_vars['a_id']]): ?>hidden<?php endif; ?>">
	<?php echo $this->_tpl_vars['content']; ?>


	<div class="buttons-container save-search">
		<div class="float-left">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/search.tpl", 'smarty_include_vars' => array('but_name' => "dispatch[".($this->_tpl_vars['dispatch'])."]",'but_role' => 'submit')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<?php if (strpos($this->_tpl_vars['_REQUEST']['dispatch'], ".picker") === false): ?>
		<div class="right">
			<?php echo fn_get_lang_var('save_this_search_as', $this->getLanguage()); ?>
:
			<input type="text" id="view_name" name="new_view" value="<?php if ($this->_tpl_vars['search']['view_id'] && $this->_tpl_vars['views'][$this->_tpl_vars['search']['view_id']]): ?><?php echo smarty_modifier_escape($this->_tpl_vars['views'][$this->_tpl_vars['search']['view_id']]['name']); ?>
<?php else: ?><?php echo fn_get_lang_var('name', $this->getLanguage()); ?>
<?php endif; ?>" class="input-save-name <?php if (! $this->_tpl_vars['search']['view_id'] && ! $this->_tpl_vars['views'][$this->_tpl_vars['search']['view_id']]): ?>cm-hint<?php endif; ?>" />
			<span class="submit-button">
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('save', $this->getLanguage()),'but_onclick' => "fn_check_views('view_name', 'views')",'but_role' => 'button')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			</span>
		</div>
		<?php endif; ?>
	</div>

	<?php ob_start(); ?>
	<?php $this->assign('items', '10', false); ?>
	<?php echo smarty_function_split(array('data' => $this->_tpl_vars['views'],'size' => $this->_tpl_vars['items'],'assign' => 'splitted_views'), $this);?>


	<div class="float-left">
		<div class="views">
			<a id="sw_views" class="cm-combo-on cm-combination"><?php if ($this->_tpl_vars['search']['view_id'] && $this->_tpl_vars['views'][$this->_tpl_vars['search']['view_id']]): ?><?php echo smarty_modifier_escape($this->_tpl_vars['views'][$this->_tpl_vars['search']['view_id']]['name']); ?>
<?php else: ?><?php echo fn_get_lang_var('all', $this->getLanguage()); ?>
<?php endif; ?></a>
			<div id="views" class="list nowrap hidden cm-popup-box">
				<div class="list-content">
				<?php if (count($this->_tpl_vars['views']) <= 10): ?>
					<ul>
				<?php endif; ?>
					<?php $_from = $this->_tpl_vars['splitted_views']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['splitted_views'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['splitted_views']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['s_views']):
        $this->_foreach['splitted_views']['iteration']++;
?>
						<?php if (count($this->_tpl_vars['views']) > 10): ?>
							<div class="float-left">
								<ul>
						<?php endif; ?>
							<?php if (($this->_foreach['splitted_views']['iteration'] <= 1)): ?>
								<li onmouseover="this.className = 'item-hover'" onmouseout="this.className = ''">
									<a href="<?php echo fn_url(($this->_tpl_vars['dispatch']).".reset_view"); ?>
"><?php echo fn_get_lang_var('all', $this->getLanguage()); ?>
</a>
								</li>
							<?php else: ?>
								<li>&nbsp;</li>
							<?php endif; ?>
							<?php $_from = $this->_tpl_vars['s_views']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['view']):
?>
							<?php if ($this->_tpl_vars['view']): ?>
							<li onmouseover="this.className = 'item-hover'" onmouseout="this.className = ''">
								<?php $this->assign('return_current_url', fn_query_remove($this->_tpl_vars['config']['current_url'], 'view_id', 'new_view'), false); ?>
								<a class="cm-view-name" rev="<?php echo $this->_tpl_vars['view']['view_id']; ?>
" href="<?php echo fn_url(($this->_tpl_vars['return_current_url'])."&amp;view_id=".($this->_tpl_vars['view']['view_id'])); ?>
"><?php echo smarty_modifier_escape($this->_tpl_vars['view']['name']); ?>
</a>
								<?php $this->assign('redirect_current_url', smarty_modifier_escape($this->_tpl_vars['config']['current_url'], 'url'), false); ?>
								<a href="<?php echo fn_url(($this->_tpl_vars['dispatch']).".delete_view?view_id=".($this->_tpl_vars['view']['view_id'])."&amp;redirect_url=".($this->_tpl_vars['redirect_current_url'])); ?>
" class="cm-confirm"><img src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/icon_delete.gif" width="12" height="18" border="0" alt="<?php echo fn_get_lang_var('delete', $this->getLanguage()); ?>
" class="hand valign" /></a>
							</li>
							<?php elseif (! $this->_tpl_vars['view'] && count($this->_tpl_vars['views']) > 10): ?>
							<li>&nbsp;</li>
							<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?>
						<?php if (count($this->_tpl_vars['views']) > 10): ?>
								</ul>
							</div>
						<?php endif; ?>
					<?php endforeach; endif; unset($_from); ?>
				<?php if (count($this->_tpl_vars['views']) <= 10): ?>
						<li class="last right">
							<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('new_saved_search', $this->getLanguage()),'but_role' => 'text','but_onclick' => "fn_advanced_search_open('".($this->_tpl_vars['a_id'])."');",'but_meta' => "text-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</li>
					</ul>
				<?php endif; ?>
				<?php if (count($this->_tpl_vars['views']) > 10): ?>
				<p class="last right">
					<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/button.tpl", 'smarty_include_vars' => array('but_text' => fn_get_lang_var('new_saved_search', $this->getLanguage()),'but_role' => 'text','but_onclick' => "fn_advanced_search_open('".($this->_tpl_vars['a_id'])."');",'but_meta' => "text-button")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</p>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php $this->_smarty_vars['capture']['title_extra'] = ob_get_contents(); ob_end_clean(); ?>
</div>

<script type="text/javascript">
//<![CDATA[
lang.object_exists = '<?php echo fn_get_lang_var('object_exists', $this->getLanguage()); ?>
';
<?php echo '
function fn_advanced_search_open(id)
{
	var elm = $(\'#\' + id);
	elm.show(); 
	jQuery.scrollToElm(elm);
	$(\'input[name=new_view]\', elm).focus();
}
function fn_check_views(input_id, views_id)
{
	var match = true;
	var sbm_button = $(\':submit:first\', $(\'#\' + input_id).parents(\'form:first\'));
	$(\'.cm-view-name\', $(\'#\' + views_id)).each(function() {
		if ($(this).text().toLowerCase() == $(\'#\' + input_id).val().toLowerCase()) {
			match = confirm(lang.object_exists);
			if (match) {
				$(\'<input type="hidden" name="update_view_id" value="\' + $(this).attr(\'rev\') + \'" />\').appendTo($(\'#\' + input_id).parent());
			}
			return false;
		}
	});
	if (match) {
		sbm_button.attr(\'name\', sbm_button.attr(\'name\').substr(0, sbm_button.attr(\'name\').length - 1) + \'.save_view]\');
		sbm_button.click();
	}
}
'; ?>

//]]>
</script>