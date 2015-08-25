{assign var="form" value=$table->getForm()}
{assign var="breadcrumbs" value=$table->getBreadcrumbs()}

{form form=$form}

    {if $table->hasRows()}
    
        {assign var="breadcrumbs" value=$table->getBreadcrumbs()}
        {if $table->hasOrderMethods() || $breadcrumbs->hasBreadcrumbs()}
        <div class="clear">    
            {if $table->hasOrderMethods() && $table->hasRows()}
                {include file="helper/table.order" form=$form}
            {/if}
            {if $table->hasSearch()}
                {include file="helper/table.search" form=$form}
            {/if}            

            {if $breadcrumbs->hasBreadcrumbs()}
                {$breadcrumbs->getHtml()}
            {/if}
        </div>
        {/if}

        {$table->getHtml()}

        {if $table->hasActions() || $table->hasPaginationOptions() || $table->getPaginationOptions()}
        <div class="clear">
            {if $table->hasActions()}
                {include file="helper/table.action" table=$table form=$form}
            {/if}
            {if $table->hasPaginationOptions() || $table->getPaginationOptions()}
                {include file="helper/table.pagination" table=$table form=$form}
            {/if}
            {include file="helper/table.export" table=$table}
        </div>
        {/if}
        
    {else}
        {if $table->hasSearch()}
            {include file="helper/table.search" form=$form}
        {/if}                
        
        {if $breadcrumbs->hasBreadcrumbs()}
            {$breadcrumbs->getHtml()}
        {/if}
    {/if}

    {include file="helper/table.js" form=$form table=$table}
{/form}