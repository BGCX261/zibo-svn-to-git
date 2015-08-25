<div class="order">
    {"table.label.order"|translate}
    {field form=$form name=$orderField}
    
    {if $table->getOrderDirection() == 'ASC'}
        {assign var="direction" value="desc"}
    {else}
        {assign var="direction" value="asc"}
    {/if}
    
    <a href="{$table->getOrderDirectionUrl()|replace:"%direction%":$direction}">
        {image src="web/images/sort.`$direction`.png"}
    </a>
</div>
