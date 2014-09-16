{* $Id: csv_delimiters.tpl 8054 2009-10-05 11:14:22Z alexions $ *}
<select name="{$name}" {if $id}id="{$id}"{/if}>
<option value="S" {if $value == "S"}selected="selected"{/if}>{$lang.semicolon}</option>
<option value="C" {if $value == "C"}selected="selected"{/if}>{$lang.comma}</option>
<option value="T" {if $value == "T"}selected="selected"{/if}>{$lang.tab}</option>
</select>
