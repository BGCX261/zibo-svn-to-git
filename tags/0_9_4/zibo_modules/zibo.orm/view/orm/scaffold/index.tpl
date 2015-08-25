<h2>{$title}</h2>

{if $actions}
<ul class="actions">
    {foreach from=$actions item="actionLabel" key="actionUrl"}
    <li><a href="{$actionUrl}">{$actionLabel}</a>
    {/foreach}
</ul>
{/if}

{include file="helper/table" table=$table}