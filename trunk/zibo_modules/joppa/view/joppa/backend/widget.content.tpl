<li class="widget pageWidget clear" id="pageWidget_{$widgetId}">
    <div class="widgetActions">
        {assign var="icon" value=$widget->getIcon()}
        {if !$icon}
            {assign var="icon" value="web/images/widget.png"}
        {/if}
        <div class="icon">
            <a href="#" class="actions">
                {image src=$icon default="web/images/widget.png"}
            </a>
        </div>
        
        <ul class="contextMenu" id="context{$widgetId}">
        {if $widget->hasProperties()}
            <li><a href="#properties/{$widgetId}" class="properties">{"joppa.button.properties"|translate}</a></li>
        {/if}
            <li><a href="#delete/{$widgetId}" class="delete">{"button.delete"|translate}</a></li>
        </ul>
        
        <div class="handle">
            {image class="handle" src="web/images/joppa/handle.png"}
        </div>
    </div>
    <div class="widgetInfo clear">
    {if $widget->hasProperties()}
        <a class="name" href="{$baseUrl}properties/{$widgetId}">{$widget->getName()}</a>
    {else}
        <span class="name">{$widget->getName()}</span>
    {/if}

    {if $widget->hasProperties()}
    <div class="preview">
        {$widget->getPropertiesPreview()}
    </div>
    {/if}
    </div>
</li>