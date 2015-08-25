<div id="spiderReport">
    <ul>
    {foreach from=$reports item="report" key="index"}
        <li><a href="#spiderReport{$index}">{$report->getTitle($translator)}</a></li>
    {/foreach}
    </ul>
    
    {foreach from=$reports item="report" key="index"}
    <div id="spiderReport{$index}">
        {subview name="report`$index`"}
    </div>
    {/foreach}
</div>