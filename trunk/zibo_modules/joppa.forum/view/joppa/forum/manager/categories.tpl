<h2>{"joppa.forum.title.manager.structure"|translate}</h2>

{if $addAction}
<ul>
    <li><a href="{$addAction}">{"joppa.forum.button.category.add"|translate}</a></li>
</ul>
{/if}

{if $categoriesTable->hasRows()}
    {include file="helper/table" table=$categoriesTable}
    
    {if $orderForm}
        {form form=$orderForm}
            {field form=$orderForm name="order"}
            {field form=$orderForm name="submit"}
        {/form}
    {/if}
{else}
    <p>{"joppa.forum.label.manager.structure.categories.none"|translate}</p>
{/if}
