<h2>{"joppa.forum.title.manager.structure"|translate}</h2>

{form form=$categoryForm}
    {field form=$categoryForm name="id"}
    
    <div class="name">
        <label for"{fieldId form=$categoryForm name="name"}">{"joppa.forum.label.manager.structure.category"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.category.description"|translate}</span>
        {field form=$categoryForm name="name"}
        {fieldErrors form=$categoryForm name="name"}
    </div>
    
    <div class="submit">
        {field form=$categoryForm name="submit"}
        {field form=$categoryForm name="cancel"}
    </div>
{/form}

{if $boardTable}
    <h3>{"joppa.forum.title.manager.boards"|translate}</h3>
    
    {if $addAction}
        <ul>
            <li><a href="{$addAction}">{"joppa.forum.button.board.add"|translate}</a></li>
        </ul>
    {/if}
    
    {if $boardTable->hasRows()}
        {include file="helper/table" table=$boardTable}
        
        {if $orderForm}
            {form form=$orderForm}
                {field form=$orderForm name="order"}
                {field form=$orderForm name="submit"}
            {/form}
        {/if}
    {else}
        <p>{"joppa.forum.label.manager.structure.boards.none"|translate}</p>
    {/if}
    
{/if}