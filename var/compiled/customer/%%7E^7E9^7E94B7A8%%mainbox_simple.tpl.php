<?php /* Smarty version 2.6.18, created on 2014-09-16 21:20:01
         compiled from blocks/wrappers/mainbox_simple.tpl */ ?>
<?php  ob_start();  ?><?php if ($this->_tpl_vars['anchor']): ?>
<a name="<?php echo $this->_tpl_vars['anchor']; ?>
"></a>
<?php endif; ?>
<div class="mainbox2-container">
	<h2 class="mainbox2-title clear"><span><?php echo $this->_tpl_vars['title']; ?>
</span></h2>
	<div class="mainbox2-body"><?php echo $this->_tpl_vars['content']; ?>
</div>
	<div class="mainbox2-bottom"><span>&nbsp;</span></div>
</div><?php  ob_end_flush();  ?>