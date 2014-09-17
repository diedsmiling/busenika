<?php /* Smarty version 2.6.18, created on 2014-09-16 21:20:01
         compiled from addons/google_analytics/hooks/index/footer.post.tpl */ ?>
<?php  ob_start();  ?>
<script type="text/javascript">
//<![CDATA[
	var _gaq = _gaq || [];
	_gaq.push(["_setAccount", "<?php echo $this->_tpl_vars['addons']['google_analytics']['tracking_code']; ?>
"]);
	_gaq.push(["_trackPageview"]);
	
	(function() <?php echo $this->_tpl_vars['ldelim']; ?>

		var ga = document.createElement("script");
		ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
		ga.setAttribute("async", "true");
		document.documentElement.firstChild.appendChild(ga);
	<?php echo $this->_tpl_vars['rdelim']; ?>
)();
//]]>
</script>
<?php  ob_end_flush();  ?>