<div id="searchResults">
    {assign var="total" value=$result->getTotalNumResults()}
    
    <p>{translate key="joppa.search.label.results" query=$query total=$total}</p>
    
    {assign var="objects" value=$result->getResults()}
    <div class="type">
        <h3>{$contentName}</h3>

        {if $pages > 1}
            {pagination page=$page pages=$pages href=$urlPage}
        {/if}

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
        
        {if $pages > 1}
            {pagination page=$page pages=$pages href=$urlPage}
        {/if}
    </div>
</div>