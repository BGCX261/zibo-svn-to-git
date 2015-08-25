<h2>{"xmlrpc.title.server"|translate}</h2>

<p>{translate key="xmlrpc.label.server.location" url=$serverUrl}<p>

{if $serverMethods}
<ul id="xmlrpcMethods">
    {foreach from=$serverMethods item="method"}
    <li>
        <div class="signature">
            <span class="signature">{$method.signature}</span>
            &rarr;            
            <span class="return">{$method.return}</span>
        </div>
        {if $method.description}
        <div class="description">{$method.description}</div>
        {/if}
    </li>
    {/foreach}
</ul>
{/if}