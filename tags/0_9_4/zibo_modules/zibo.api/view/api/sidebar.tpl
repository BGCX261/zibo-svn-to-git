<div class="api">
{if $searchForm}
    {form form=$searchForm}
        {field form=$searchForm name="query"}
        {field form=$searchForm name="submit"}
    {/form}
{/if}

{if $classes}
    <h3>{"api.title.classes"|translate}</h3>

    <ul class="classes">
    {foreach from=$classes item="className" key="class}
        <li{if $className == $currentClass} class="current"{/if}>
            <a href="{$classAction}{$class}" title="{$currentNamespace|replace:'/':'\\'}\{$className}">{$className}</a>
        </li>
    {/foreach}
    </ul>
{/if}

{if $namespaces}
    <h3>{"api.title.namespaces"|translate}</h3>

    <ul class="namespaces">
    {foreach from=$namespaces item="namespace"}
        <li>
            <a href="{$namespaceAction}{$namespace}" title="{$namespace|replace:'/':'\\'}">{$namespace|replace:'/':'\\'}</a>
        </li>
    {/foreach}
    </ul>
{/if}
</div>