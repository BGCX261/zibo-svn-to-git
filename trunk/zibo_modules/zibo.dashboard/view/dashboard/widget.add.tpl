<div id="dashboard">

    <h2>{"dashboard.title.widget.add"|translate}</h2>

    <p><a href="{$returnAction}">{"dashboard.button.return.to.dashboard"|translate}</a></p>

    <div class="widgetAdd">
{foreach from=$widgets item="widgetObjects" key="namespace"}
        <h3>{$namespace|capitalize}</h3>
    {foreach from=$widgetObjects item="widget" key="name"}
        <div class="widget">
            <div class="name">
        {assign var="image" value=$widget->getIcon()}
        {if $image}
            {image src=$image}
        {else}
            {image src="web/images/dashboard/widget.png"}
        {/if}    
            {$widget->getName()}
            </div>
            <a href="#" onclick="return dashboardAddWidget('{$namespace}', '{$name}', this);">{translate key="dashboard.button.add.to.dashboard" widget=$widget->getName()}</a>
        </div>    
    {/foreach}
{/foreach}
    </div>
</div>