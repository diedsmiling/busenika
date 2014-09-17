<?php /* Smarty version 2.6.18, created on 2014-09-16 21:19:58
         compiled from blocks/wrappers/mainbox_general.tpl */ ?>
<?php  ob_start();  ?><?php if ($this->_tpl_vars['anchor']): ?>
<a name="<?php echo $this->_tpl_vars['anchor']; ?>
"></a>
<?php endif; ?>
<div class="mainbox-container<?php if ($this->_tpl_vars['details_page']): ?> details-page<?php endif; ?>">
	<?php if ($this->_tpl_vars['title']): ?>
		<?php 		
		if($_GET['dispatch'] != 'product_features.view'):        			
		 ?>
	<span class="mainbox-title"><span><?php echo $this->_tpl_vars['title']; ?>
</span></span>
		<?php 		
			endif;
		 ?>
	<?php endif; ?>
	<div class="mainbox-body"><?php echo $this->_tpl_vars['content']; ?>
</div> 
</div>
<?php  ob_end_flush();  ?>