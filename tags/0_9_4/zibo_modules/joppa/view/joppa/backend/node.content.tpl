<h2>{"joppa.title.content"|translate}: {$page->name}</h2>

{form form=$form}
    {"joppa.label.region.select"|translate}
    {field form=$form name="region"}
{/form}

{if $region}
<div id="contentRegion">
    <h3>{"joppa.title.widgets.content"|translate}</h3>
    <p>{"joppa.label.widgets.content"|translate}</p>
    
    <ul class="droppable">
    	{foreach from=$regionWidgets item="widget" key="widgetId"}
            {include file="joppa/backend/widget.content"}
    	{/foreach}
    </ul>
</div>

<div id="widgets">
    <h3>{"joppa.title.widgets.available"|translate}</h3>
    <p>{"joppa.label.widgets.available"|translate}</p>
    
	{foreach from=$availableWidgets item="widgets" key="namespace"}
	<div class="widgetNamespace">
    	<a href="#" class="namespace">{$namespace|ucfirst}</a>
    	<ul>
    		{foreach from=$widgets item="widget" key="name"}
    			{assign var="icon" value=$widget->getIcon()}
    			{if !$icon}
    				{assign var="icon" value="web/images/widget.png"}
    			{/if}
        	<li class="widget" id="widget_{$namespace}_{$name}">
        		{image class="handle" src=$icon default="web/images/widget.png"}
            	{$widget->getName()}
    		</li>
    		{/foreach}
    	</ul>
	</div>
	{/foreach}
</div>
{/if}