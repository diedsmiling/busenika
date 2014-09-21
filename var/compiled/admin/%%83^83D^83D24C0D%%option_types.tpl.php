<?php /* Smarty version 2.6.18, created on 2014-09-18 22:51:46
         compiled from views/product_options/components/option_types.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strpos', 'views/product_options/components/option_types.tpl', 28, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('selectbox','radiogroup','checkbox','text','textarea','file','selectbox','radiogroup','checkbox','text','textarea','file'));
?>
<?php  ob_start();  ?><?php echo ''; ?><?php if ($this->_tpl_vars['display'] == 'view'): ?><?php echo ''; ?><?php if ($this->_tpl_vars['value'] == 'S'): ?><?php echo ''; ?><?php echo fn_get_lang_var('selectbox', $this->getLanguage()); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['value'] == 'R'): ?><?php echo ''; ?><?php echo fn_get_lang_var('radiogroup', $this->getLanguage()); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['value'] == 'C'): ?><?php echo ''; ?><?php echo fn_get_lang_var('checkbox', $this->getLanguage()); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['value'] == 'I'): ?><?php echo ''; ?><?php echo fn_get_lang_var('text', $this->getLanguage()); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['value'] == 'T'): ?><?php echo ''; ?><?php echo fn_get_lang_var('textarea', $this->getLanguage()); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['value'] == 'F'): ?><?php echo ''; ?><?php echo fn_get_lang_var('file', $this->getLanguage()); ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php if ($this->_tpl_vars['value']): ?><?php echo ''; ?><?php if ($this->_tpl_vars['value'] == 'S' || $this->_tpl_vars['value'] == 'R'): ?><?php echo ''; ?><?php $this->assign('app_types', 'SR', false); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['value'] == 'I' || $this->_tpl_vars['value'] == 'T'): ?><?php echo ''; ?><?php $this->assign('app_types', 'IT', false); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['value'] == 'C'): ?><?php echo ''; ?><?php $this->assign('app_types', 'C', false); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php $this->assign('app_types', 'F', false); ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php $this->assign('app_types', "", false); ?><?php echo ''; ?><?php endif; ?><?php echo '<select id="'; ?><?php echo $this->_tpl_vars['tag_id']; ?><?php echo '" name="'; ?><?php echo $this->_tpl_vars['name']; ?><?php echo '" '; ?><?php if ($this->_tpl_vars['check']): ?><?php echo 'onchange="fn_check_option_type(this.value, this.id);"'; ?><?php endif; ?><?php echo '>'; ?><?php if (! $this->_tpl_vars['app_types'] || ( $this->_tpl_vars['app_types'] && strpos($this->_tpl_vars['app_types'], 'S') !== false )): ?><?php echo '<option value="S" '; ?><?php if ($this->_tpl_vars['value'] == 'S'): ?><?php echo 'selected="selected"'; ?><?php endif; ?><?php echo '>'; ?><?php echo fn_get_lang_var('selectbox', $this->getLanguage()); ?><?php echo '</option>'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_tpl_vars['app_types'] || ( $this->_tpl_vars['app_types'] && strpos($this->_tpl_vars['app_types'], 'R') !== false )): ?><?php echo '<option value="R" '; ?><?php if ($this->_tpl_vars['value'] == 'R'): ?><?php echo 'selected="selected"'; ?><?php endif; ?><?php echo '>'; ?><?php echo fn_get_lang_var('radiogroup', $this->getLanguage()); ?><?php echo '</option>'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_tpl_vars['app_types'] || ( $this->_tpl_vars['app_types'] && strpos($this->_tpl_vars['app_types'], 'C') !== false )): ?><?php echo '<option value="C" '; ?><?php if ($this->_tpl_vars['value'] == 'C'): ?><?php echo 'selected="selected"'; ?><?php endif; ?><?php echo '>'; ?><?php echo fn_get_lang_var('checkbox', $this->getLanguage()); ?><?php echo '</option>'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_tpl_vars['app_types'] || ( $this->_tpl_vars['app_types'] && strpos($this->_tpl_vars['app_types'], 'I') !== false )): ?><?php echo '<option value="I" '; ?><?php if ($this->_tpl_vars['value'] == 'I'): ?><?php echo 'selected="selected"'; ?><?php endif; ?><?php echo '>'; ?><?php echo fn_get_lang_var('text', $this->getLanguage()); ?><?php echo '</option>'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_tpl_vars['app_types'] || ( $this->_tpl_vars['app_types'] && strpos($this->_tpl_vars['app_types'], 'T') !== false )): ?><?php echo '<option value="T" '; ?><?php if ($this->_tpl_vars['value'] == 'T'): ?><?php echo 'selected="selected"'; ?><?php endif; ?><?php echo '>'; ?><?php echo fn_get_lang_var('textarea', $this->getLanguage()); ?><?php echo '</option>'; ?><?php endif; ?><?php echo ''; ?><?php if (! $this->_tpl_vars['app_types'] || ( $this->_tpl_vars['app_types'] && strpos($this->_tpl_vars['app_types'], 'F') !== false )): ?><?php echo '<option value="F" '; ?><?php if ($this->_tpl_vars['value'] == 'F'): ?><?php echo 'selected="selected"'; ?><?php endif; ?><?php echo '>'; ?><?php echo fn_get_lang_var('file', $this->getLanguage()); ?><?php echo '</option>'; ?><?php endif; ?><?php echo '</select>'; ?><?php endif; ?><?php echo ''; ?>
<?php  ob_end_flush();  ?>