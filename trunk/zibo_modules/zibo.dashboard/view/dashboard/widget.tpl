<div class="widget" id="widget{$widgetId}">
    <div class="titleBar">
        <div class="buttons">
            <a class="close" href="#" onclick="return dashboardCloseWidget({$widgetId});" title="{"dashboard.button.close"|translate}"></a>
            <a class="{if $minimized}maximize{else}minimize{/if}" href="#" onclick="return dashboardMinimizeMaximizeWidget({$widgetId});" title="{"dashboard.button.minimize.maximize"|translate}"></a>
        </div>        
        {if $propertiesAction}
        <span><a href="{$propertiesAction}" title="{"dashboard.button.properties"|translate}">{$title}</a></span>
        {else}
        <span>{$title}</span>
        {/if}
    </div>
    <div class="content"{if $minimized} style="display: none"{/if}>
        {$content}
    </div>
</div>