{if $name && isset($regions.$name)}
    {foreach from=$regions.$name item="widgets"}
        {foreach from=$widgets item="widget"}
            {$widget}
        {/foreach}
    {/foreach}
{/if}