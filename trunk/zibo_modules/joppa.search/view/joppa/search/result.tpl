{if !$result}

<p>{"joppa.search.label.no.query"|translate}</p>

{else}

<div id="searchResults">
    {assign var="total" value=$result->getNumResults()}
    
    {if $total == 1}
        <p>{translate key="joppa.search.label.result" query=$query}</p>
    {else}
        <p>{translate key="joppa.search.label.results" query=$query total=$total}</p>
    {/if}
    
    {assign var="results" value=$result->getResults()}
    {foreach from=$results item="contentResult" key="contentName"}
        {assign var="objects" value=$contentResult->getResults()}
        {if $objects}
        <div class="type">
            <h3>{$contentName}</h3>
            {foreach from=$objects item="object"}
                <div>
                {if $object->image}
                    <div class="image">
                    {if !$object->url}
                        {image src=$object->image thumbnail="crop" width=40 height=40}
                    {else}
                        <a href="{$object->url}">{image src=$object->image thumbnail="crop" width=40 height=40}</a>
                    {/if}
                    </div>
                {/if}   
                {if !$object->url}
                    <h4>{$object->title}</h4>
                {else}
                    <h4><a href="{$object->url}">{$object->title}</a></h4>
                {/if}
                {if $object->teaser}
                    <div class="teaser">{$object->teaser}</div>
                {/if}
                    <br class="clear" />
                </div>
            {/foreach}
            {if $urlMore}
                {assign var="numResults" value=$contentResult->getNumResults()}
                {assign var="totalNumResults" value=$contentResult->getTotalNumResults()}
                {assign var="remainingResults" value=$totalNumResults-$numResults}
                {if $remainingResults}
                <div class="more">
                    <a href="{$urlMore}{$contentName}/{$query}">{translate key="joppa.search.button.more" num=$remainingResults}</a>
                </div>
                {/if}
            {/if}
        </div>
        {/if}
    {/foreach}
</div>

{/if}