<div class="contentList" id="widget{$widgetId}">
{if $title}
    <div class="title">{$title}</div>
{/if}

{if $result}
    <ul class="result">
    {foreach from=$result item="content"}
        <li class="{cycle values="odd,even"}">{$content->title}</li>
    {/foreach}
    </ul>
    
    {if $pagination}
        {pagination href=$pagination->getUrl() pages=$pagination->getPages() page=$pagination->getPage()}
    {/if}
    
    {if $moreUrl}
    <a href="{$moreUrl}" class="more">{$moreLabel}</a>
    {/if}
{else}
    <p>{$emptyResultMessage}</p>
{/if}
</div>