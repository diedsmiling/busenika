{* $Id: content.post.tpl 9353 2010-05-04 06:10:09Z klerik $ *}

<td>{if $person.type == "chat"}{assign var="person_ip" value=$person.ip|escape:url}<a href="{"statistics.visitors?report=by_ip&amp;ip=`$person_ip`"|fn_url}">{$lang.view}&nbsp;&raquo;</a>{else}&nbsp;{/if}</td>