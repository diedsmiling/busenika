{* $Id: option_types.tpl 9379 2010-05-05 13:06:03Z alexions $ *}
{strip}
{if $display == "view"}
	{if $value == "S"}{$lang.selectbox}
	{elseif $value == "R"}{$lang.radiogroup}
	{elseif $value == "C"}{$lang.checkbox}
	{elseif $value == "I"}{$lang.text}
	{elseif $value == "T"}{$lang.textarea}
	{elseif $value == "F"}{$lang.file}
	{/if}
{else}

{if $value}
	{if $value == "S" || $value == "R"}
		{assign var="app_types" value="SR"}
	{elseif $value == "I" || $value == "T"}
		{assign var="app_types" value="IT"}
	{elseif $value == "C"}
		{assign var="app_types" value="C"}
	{else}
		{assign var="app_types" value="F"}
	{/if}
{else}
	{assign var="app_types" value=""}
{/if}

<select id="{$tag_id}" name="{$name}" {if $check}onchange="fn_check_option_type(this.value, this.id);"{/if}>
{if !$app_types || ($app_types && $app_types|strpos:"S" !== false)}
<option value="S" {if $value == "S"}selected="selected"{/if}>{$lang.selectbox}</option>
{/if}
{if !$app_types || ($app_types && $app_types|strpos:"R" !== false)}
<option value="R" {if $value == "R"}selected="selected"{/if}>{$lang.radiogroup}</option>
{/if}
{if !$app_types || ($app_types && $app_types|strpos:"C" !== false)}
<option value="C" {if $value == "C"}selected="selected"{/if}>{$lang.checkbox}</option>
{/if}
{if !$app_types || ($app_types && $app_types|strpos:"I" !== false)}
<option value="I" {if $value == "I"}selected="selected"{/if}>{$lang.text}</option>
{/if}
{if !$app_types || ($app_types && $app_types|strpos:"T" !== false)}
<option value="T" {if $value == "T"}selected="selected"{/if}>{$lang.textarea}</option>
{/if}
{if !$app_types || ($app_types && $app_types|strpos:"F" !== false)}
<option value="F" {if $value == "F"}selected="selected"{/if}>{$lang.file}</option>
{/if}
</select>

{/if}
{/strip}