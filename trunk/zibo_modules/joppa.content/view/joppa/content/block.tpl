<div class="contentOverviewBlock" id="widget{$widgetId}">
{if $title}
    <div class="title">{$title}</div>
{/if}

{if $result}
    {foreach from=$result item="content"}
    <div class="content clear {cycle values="odd,even"}">
        {if $content->image}
        <div class="image">
            {if $content->url}
            <a href="{$content->url}">{image src=$content->image width=125 height=125 thumbnail="resize"}</a>
            {else}
            {image src=$content->image width=125 height=125 thumbnail="resize"}
            {/if}
        </div>
        {/if}
        <div class="title">{if $content->url}<a href="{$content->url}">{$content->title}</a>{else}{$content->title}{/if}</div>
        <div class="teaser">{$content->teaser}</div>
    </div>
    {/foreach}
    
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