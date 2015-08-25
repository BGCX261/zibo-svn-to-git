<div id="taskbar">
    <div class="helper">
{if $settingsMenu->hasItems()}
        <ul class="settings" id="taskbarSettings">
            <li class="menu">
                <a href="#" class="menu">{"admin.button.settings"|translate}</a>
                {$settingsMenu->getHtml()}
            </li>
        </ul>
{/if}

{if $notificationPanels}
        <ul class="panels">
    {foreach from=$notificationPanels item="panel"}
            <li>{$panel}</li>
    {/foreach}
        </ul>
{/if}
    </div>
    
    <div class="main">
        <h1><a href="{$_baseUrl}">{$title}</a></h1>
        {$applicationsMenu->getHtml()}
    </div>
</div>