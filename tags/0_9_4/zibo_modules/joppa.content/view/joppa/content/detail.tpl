<div class="contentDetail" id="widget{$widgetId}">
    <div class="content clear">
        {if $content->image}
        <div class="image">
            {image src=$content->image width=125 height=125 thumbnail="resize"}
        </div>
        {/if}
        <div class="title">{$content->title}</div>
        <div class="teaser">{$content->teaser}</div>
    </div>
    
    <a class="back" href="#" onclick="history.go(-1);">{"button.back"|translate}</a>
</div>