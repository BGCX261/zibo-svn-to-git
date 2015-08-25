<p>{"joppa.text.label.properties"|translate}</p>

{form form=$form}
    {field form=$form name="id"}
    
    {field form=$form name="version"}
    {fieldErrors form=$form name="version"}
    
    <div class="text">
        {field form=$form name="text"}
        {fieldErrors form=$form name="text"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}

{if $history}

<h3>{"joppa.text.title.history"|translate}</h3>
<p>{"joppa.text.label.history"|translate}</p>

<ul class="history">
{foreach from=$history item="log" name="history"}
    {if $smarty.foreach.history.index == 3}
</ul>
<p><a href="#" id="textHistoryMore">{"joppa.text.button.more"|translate}</a></p>
<ul class="historyMore">
    {/if}
    <li{if $log->dataVersion === $currentVersion} class="selected"{/if}><a href="{$historyUrl}{$log->dataVersion}">{$log->dateAdded|formatDate} {$log->dateAdded|formatDate:"H:i:s"}</a></li>
{/foreach}
</ul>

{/if}