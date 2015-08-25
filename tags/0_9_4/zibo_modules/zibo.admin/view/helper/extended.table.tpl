{if $table->hasRows() || $table->hasSearch()}
    {assign var="form" value=$table->getForm()}

    {form form=$form}
    
        {if $table->hasOrderMethods() || $table->hasSearch()}
        <div class="clear">
            {if $table->hasOrderMethods()}
                {include file="helper/table.order" form=$form}
            {/if}
            {if $table->hasSearch()}
                {include file="helper/table.search" form=$form}
            {/if}
        </div>
        {/if}
    
        {if $table->hasRows()}
            {$table->getHtml()}
    
            {if $table->hasActions() || $table->hasPaginationOptions()}
            <div class="clear">
                {if $table->hasActions()}
                    {include file="helper/table.action" table=$table form=$form}
                {/if}
                {if $table->hasPaginationOptions()}
                    {include file="helper/table.pagination" table=$table form=$form}
                {/if}
                {include file="helper/table.export" table=$table}
            </div>
            {/if}
        {/if}
    {/form}
    
    {include file="helper/table.js" form=$form table=$table}   
{/if}