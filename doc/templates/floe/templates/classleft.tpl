<ul>
{foreach key=subpackage item=files from=$classleftindex}
	{if $subpackage != ""}
		<li>{$subpackage|replace:'-':'.'}<ul>
	{/if}
	{section name=files loop=$files}
		{if $files[files].link != ''}<li><a href="{$files[files].link}">{/if}
		{$files[files].title}
		{if $files[files].link != ''}</a>{/if}</li>
	{/section}
	{if $subpackage != ""}</ul></li>{/if}
{/foreach}
</ul>
