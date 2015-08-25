<div id="api">
    {$breadcrumbs->getHtml()}
        
    <div class="detail">
        <h2>{translate key="api.title.search" query=$searchQuery}</h2>
        
        <ul>
        {foreach from=$searchResult item="name" key="class"}
            <li><a href="{$classAction}{$class}" title="{$class}">{$class}</a></li>
        {/foreach}
        </ul>
    </div>
</div>