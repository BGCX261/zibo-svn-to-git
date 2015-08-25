{if $locales}
<ul id="modelLocalize">
    {foreach from=$locales item="label" key="locale"}
    <li>
        {if $label != null}
            {assign var="label" value=$label->localizedLabel}
        <strong>{$locale}</strong>:
                {if $action}
        <a href="{$action}/{$locale}">{$label}</a>
                {else}
        {$label}
                {/if}
        {else}
        {$locale}:
            {if $action} 
        <a href="{$action}/{$locale}">{"orm.button.localize"|translate}</a>
            {else}
            ---
            {/if}
        {/if}
    </li>
    {/foreach}
</ul>
{/if}