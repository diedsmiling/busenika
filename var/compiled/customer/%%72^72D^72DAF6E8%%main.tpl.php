<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:57
         compiled from main.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'block', 'main.tpl', 3, false),array('modifier', 'trim', 'main.tpl', 7, false),array('block', 'hook', 'main.tpl', 8, false),)), $this); ?>

<?php echo smarty_function_block(array('group' => 'top','assign' => 'top'), $this);?>

<?php echo smarty_function_block(array('group' => 'left','assign' => 'left'), $this);?>

<?php echo smarty_function_block(array('group' => 'right','assign' => 'right'), $this);?>

<?php echo smarty_function_block(array('group' => 'bottom','assign' => 'bottom'), $this);?>

<div id="container" class="container<?php if (! trim($this->_tpl_vars['left']) && ! trim($this->_tpl_vars['right'])): ?>-long<?php elseif (! trim($this->_tpl_vars['left'])): ?>-left<?php elseif (! trim($this->_tpl_vars['right'])): ?>-right<?php endif; ?>">
	<?php $this->_tag_stack[] = array('hook', array('name' => "index:main_content")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<div id="header"><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "top.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
	<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
	<div class="wrapper">
		<div id="content">
			<div class="content-helper clear">
				<?php if (trim($this->_tpl_vars['top'])): ?>
				<div class="header">
					<?php echo $this->_tpl_vars['top']; ?>

				</div>
				<?php endif; ?>
				
				
				<?php if (trim($this->_tpl_vars['left'])): ?>
				<div class="left-column">
					<?php echo $this->_tpl_vars['left']; ?>

				</div>
				<?php endif; ?>
				
				
				<?php $this->_tag_stack[] = array('hook', array('name' => "index:columns")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
				<div class="central-column">
					<div class="central-content">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/breadcrumbs.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						<?php echo smarty_function_block(array('group' => 'central'), $this);?>

						<?php 
						   //  if (!defined('_LLM_DOMAIN_KEY')){
						   //  define('_LLM_DOMAIN_KEY', '8d4f638e835332d66ae64bc5c93d0492'); 
						   //  }
						   //  require_once($_SERVER['DOCUMENT_ROOT'].'/llm-'._LLM_DOMAIN_KEY.'/llm.php'); 
						   //  $llm = new LLM_client();
						   //  echo $llm->return_links();
						 ?>
					</div>
				</div>
			
				
				<?php if (trim($this->_tpl_vars['right'])): ?>
				<div class="right-column">
					<?php echo $this->_tpl_vars['right']; ?>

				</div>
				<?php endif; ?>
				<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
				
				<?php if (trim($this->_tpl_vars['bottom'])): ?>
				<div class="bottom clear-both">
					<?php echo $this->_tpl_vars['bottom']; ?>

				</div>
				<?php endif; ?>
			</div>
		</div>
		
		<div id="footer">
			<div class="footer-helper-container">
				<div class="footer-top-helper"><span class="float-left">&nbsp;</span><span class="float-right">&nbsp;</span></div>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<div class="footer-bottom-helper"><span class="float-left">&nbsp;</span><span class="float-right">&nbsp;</span></div>
			</div>
		</div>
	</div>
</div>