<?php /* Smarty version 2.6.18, created on 2014-09-23 21:21:00
         compiled from bottom.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'hook', 'bottom.tpl', 73, false),array('modifier', 'fn_url', 'bottom.tpl', 76, false),array('modifier', 'date_format', 'bottom.tpl', 82, false),array('modifier', 'defined', 'bottom.tpl', 143, false),array('modifier', 'fn_check_meta_redirect', 'bottom.tpl', 150, false),)), $this); ?>

<div class="bottom-search center">
	<span class="float-left">&nbsp;</span>
	<span class="float-right">&nbsp;</span>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/search.tpl", 'smarty_include_vars' => array('hide_advanced_search' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div class="social-footer">

<?php echo '




<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter21813562 = new Ya.Metrika({id:21813562,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/21813562" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->








<div id="vk_like"></div>
<script type="text/javascript">
VK.Widgets.Like("vk_like", {type: "mini"});
</script>
<div class="footer-fb">
	<fb:like href="http://korzin.net" send="false" layout="button_count" width="450" show_faces="true"></fb:like>
</div>

<div class="footer-ok">
<a target="_blank" class="mrc__plugin_uber_like_button" href="http://connect.mail.ru/share" data-mrc-config="{\'cm\' : \'1\', \'ck\' : \'1\', \'sz\' : \'20\', \'st\' : \'3\', \'tp\' : \'ok\'}">Нравится</a>
<script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript" charset="UTF-8"></script>
</div>        

<div class="footer-tw">
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://korzin.net" data-lang="ru" data-dnt="true">Твитнуть</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>	
</div>
       
'; ?>



</div>
<?php $this->_tag_stack[] = array('hook', array('name' => "index:bottom_links")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<p class="quick-links">
	<?php $_from = $this->_tpl_vars['quick_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['link']):
?>
		<a href="<?php echo fn_url($this->_tpl_vars['link']['param']); ?>
"><?php echo $this->_tpl_vars['link']['descr']; ?>
</a>
	<?php endforeach; endif; unset($_from); ?>
</p>
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>
<?php $this->_tag_stack[] = array('hook', array('name' => "index:bottom")); $_block_repeat=true;smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
<p class="bottom-copyright class">&copy; <?php if (smarty_modifier_date_format(@TIME, "%Y") != $this->_tpl_vars['settings']['Company']['company_start_year']): ?><?php echo $this->_tpl_vars['settings']['Company']['company_start_year']; ?>
-<?php endif; ?><?php echo smarty_modifier_date_format(@TIME, "%Y"); ?>
 <?php echo $this->_tpl_vars['settings']['Company']['company_name']; ?>
. &nbsp;| +7 (495) 943-55-04 | +7 (905) 724-50-05
</p>


<div style="margin: 0 auto; width:308px;">
<?php echo '
   <!-- Rating@Mail.ru counter -->
<script type="text/javascript">//<![CDATA[
var a=\'\',js=10;try{a+=\';r=\'+escape(document.referrer);}catch(e){}try{a+=\';j=\'+navigator.javaEnabled();js=11;}catch(e){}
try{s=screen;a+=\';s=\'+s.width+\'*\'+s.height;a+=\';d=\'+(s.colorDepth?s.colorDepth:s.pixelDepth);js=12;}catch(e){}
try{if(typeof((new Array).push(\'t\'))==="number")js=13;}catch(e){}
try{document.write(\'<a href="http://top.mail.ru/jump?from=2197788">\'+
\'<img src="http://d9.c8.b1.a2.top.mail.ru/counter?id=2197788;t=218;js=\'+js+a+\';rand=\'+Math.random()+
\'" alt="Ðåéòèíã@Mail.ru" style="border:0;" height="31" width="88" \\/><\\/a>\');}catch(e){}//]]></script>
<noscript><p><a href="http://top.mail.ru/jump?from=2197788">
<img src="http://d9.c8.b1.a2.top.mail.ru/counter?js=na;id=2197788;t=218" 
style="border:0;" height="31" width="88" alt="Ðåéòèíã@Mail.ru" /></a></p></noscript>
<!-- //Rating@Mail.ru counter -->
</ br>
<a href="http://yandex.ru/cy?base=0&amp;host=korzin.ret"><img src="http://www.yandex.ru/cycounter?korzin.ret" width="88" height="31" alt="ßíäåêñ öèòèðîâàíèÿ" border="0" /></a>
<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href=\'http://www.liveinternet.ru/click\' "+
"target=_blank><img src=\'//counter.yadro.ru/hit?t52.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"\' alt=\'\' title=\'LiveInternet: ïîêàçàíî ÷èñëî ïðîñìîòðîâ è"+
" ïîñåòèòåëåé çà 24 ÷àñà\' "+
"border=\'0\' width=\'88\' height=\'31\'><\\/a>")
//--></script><!--/LiveInternet-->

<!-- begin of Onlinesaler code -->

<script type="text/javascript">var _oaq = _oaq || [];_oaq.push([\'_OPAccount\', \'99\']);(function() {var oa = document.createElement(\'script\'); oa.type = \'text/javascript\'; oa.async = true; oa.src = \'http://onlinesaler.ru/assets/templates/os2013/common/js.php?domen=korzin.net&id=\'; var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(oa, s);  })();</script>

<!-- begin of Top100 code -->



<script id="top100Counter" type="text/javascript" src="http://counter.rambler.ru/top100.jcn?2708352"></script>
<noscript>
<a href="http://top100.rambler.ru/navi/2708352/">
<img src="http://counter.rambler.ru/top100.cnt?2708352" alt="Rambler\'s Top100" border="0" />
</a>

</noscript>
<!-- end of Top100 code --!>
<a href="http://www.fbnp.ru" target="_blank"><IMG alt="fbnp" title="fbnp" src="http://www.fbnp.ru/images/fbnp_v2.gif" border=0></a>
'; ?>


</div>




<?php if ($this->_tpl_vars['addons']['affiliate']['status'] == 'A'): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "addons/affiliate/hooks/index/bottom.post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?><?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_hook($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

<?php if ($this->_tpl_vars['manifest']['copyright']): ?>
<?php endif; ?>

<?php if (defined('DEBUG_MODE')): ?>
<div class="bug-report">
	<input type="button" onclick="window.open('bug_report.php','popupwindow','width=700,height=450,toolbar=yes,status=no,scrollbars=yes,resizable=no,menubar=yes,location=no,direction=no');" value="Report a bug" />
</div>

<?php endif; ?>

<?php if (fn_check_meta_redirect($this->_tpl_vars['_REQUEST']['meta_redirect_url'])): ?>
	<meta http-equiv="refresh" content="1;url=<?php echo fn_url(fn_check_meta_redirect($this->_tpl_vars['_REQUEST']['meta_redirect_url'])); ?>
" />
<?php endif; ?>
