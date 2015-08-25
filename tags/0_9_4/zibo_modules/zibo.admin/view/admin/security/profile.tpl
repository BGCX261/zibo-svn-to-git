{pageTitle}

{form form=$form}
    {foreach from=$subviewNames item="subviewName" name="hookSubviews"}
        {subview name=$subviewName}
        
        {if !$smarty.foreach.hookSubviews.last}
            <hr />
        {/if}
    {/foreach}
{/form}