<h2>{"queue.title"|translate}</h2>

{if $table->hasRows()}
    {include file="helper/table" table=$table}
{else}
    <p>{"queue.label.empty"|translate}</p>
{/if}