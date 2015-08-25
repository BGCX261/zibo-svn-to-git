<div id="dashboard">
{if $addAction}
    <p class="addAction"><a href="{$addAction}">{"dashboard.button.add"|translate}</a></p>
{/if}

{foreach from=$columns item="column" key="columnNumber"}
    <div class="column" id="column{$columnNumber}">
    {foreach from=$column item="widget"}
        {$widget}
    {/foreach}
    </div>
{/foreach}
    <br class="clear" />

</div>