{if $table->getRowsPerPage() || $table->getPaginationOptions()}
    {assign var="page" value=$table->getPage()}
    {assign var="pages" value=$table->getPages()}
    {assign var="href" value=$table->getPaginationUrl()}
    
    {pagination page=$page pages=$pages href=$href}

    <div class="pageItems">
    {if $table->getPaginationOptions()}
        {field form=$form name=$pageRowsField}
        {"table.label.rows.page"|translate}
    {/if}
    
        ({translate key="table.label.rows.total" rows=$table->countRows()})
    </div>
{else}
    <div class="pageItems">
        {translate key="table.label.rows.total" rows=$table->countRows()}
    </div>
{/if}