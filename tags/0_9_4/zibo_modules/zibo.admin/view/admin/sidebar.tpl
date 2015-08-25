<div id="sidebar">
    <a href="#" id="toggleButton"><span>toggle</span></a>
    <div class="container">
    
{if $information}
        <div class="panel{if !$panels && !$actions} last{/if}" id="sidebarInformation">
            {$information}            
        </div>
{/if}

{if $actions}
        <div class="panel{if !$panels} last{/if}" id="sidebarActions">
            <ul>
    {foreach from=$actions item="label" key="url"}
                <li><a href="{$url}">{$label}</a></li>
    {/foreach}
            </ul>
        </div>
{/if}

{if $panels}
    {foreach from=$panels item="panel" name="panels"}
        <div class="panel{if $smarty.foreach.panels.last} last{/if}" id="sidebarPanels">
            {$panel}                
        </div>
    {/foreach}
{/if}
    </div>
</div>