<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$maintitle|lower} . {$title|lower}</title>
	<link rel="stylesheet" type="text/css" id="layout" href="{$subdir}media/layout.css" media="screen">
	<link rel="stylesheet" type="text/css" href="{$subdir}media/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="{$subdir}media/print.css" media="print">
</head>

<body>
<div id="header">
	<div id="logo">
		<h1>{include file="headertitle.tpl"}</h1>
	</div>
	<div class="package-nav">
		<ul>
		{section name=packagelist loop=$packageindex}
			<li><a href="{$subdir}{$packageindex[packagelist].link}">{$packageindex[packagelist].title}</a></li>
		{/section}
		</ul>
	</div>
</div>

<div id="content">

	
</div>

<div id="nav" class="small">

	<div class="utility-nav">
		<ul>
		{if $hastodos}
			<li><a href="{$subdir}{$todolink}">Todo List</a></li>
		{/if}
			<li><a href="{$subdir}elementindex.html">A-Z Index</a></li>
		</ul>
	</div>

	<div id="navLinks">
		<ul>
        {assign var="packagehaselements" value=false}
        {foreach from=$packageindex item=thispackage}
            {if in_array($package, $thispackage)}
                {assign var="packagehaselements" value=true}
            {/if}
        {/foreach}
        {if $packagehaselements}
	        <li><a href="{$subdir}classtrees_{$package}.html">{$package} Class Tree</a></li>
            <li><a href="{$subdir}elementindex_{$package}.html">Index: {$package}</a></li>
        {/if}
		</ul>
	</div>

{if count($ric) >= 1}
	<div id="ric">
		{section name=ric loop=$ric}
			<p><a href="{$subdir}{$ric[ric].file}">{$ric[ric].name}</a></p>
		{/section}
	</div>
{/if}

{if $tutorials}
	<div id="tutorials">
		Tutorials/Manuals:<br />
		{if $tutorials.pkg}
			<strong>Package-level:</strong>
			{section name=ext loop=$tutorials.pkg}
				{$tutorials.pkg[ext]}
			{/section}
		{/if}
		{if $tutorials.cls}
			<strong>Class-level:</strong>
			{section name=ext loop=$tutorials.cls}
				{$tutorials.cls[ext]}
			{/section}
		{/if}
		{if $tutorials.proc}
			<strong>Procedural-level:</strong>
			{section name=ext loop=$tutorials.proc}
				{$tutorials.proc[ext]}
			{/section}
		{/if}
	</div>
{/if}

	{if !$noleftindex}{assign var="noleftindex" value=false}{/if}
	{if !$noleftindex}
		<div id="index">
			<div id="interfaces">
				{if $compiledinterfaceindex}Interfaces:<br>
				{eval var=$compiledinterfaceindex}{/if}
			</div>
			<div id="classes">
				{if $compiledclassindex}Classes:<br>
				{eval var=$compiledclassindex}{/if}
			</div>
		</div>
	{/if}
</div>

<div id="body">
	{if !$hasel}{assign var="hasel" value=false}{/if}
    {if $eltype == 'class' && $is_interface}{assign var="eltype" value="interface"}{/if}
	{if $hasel}
	<h2>{$eltype|capitalize}: {$class_name}</h2>
	<p><code>Source Location: {$source_location}</code></p>
	{/if}
