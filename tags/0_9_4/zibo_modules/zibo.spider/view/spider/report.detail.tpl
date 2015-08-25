<div id="spiderDetail" class="detail">
{assign var="response" value=$node->getResponse()}
{assign var="links" value=$node->getLinks()}
{assign var="references" value=$node->getReferences()}

{if $response}
    {assign var="responseCode" value=$response->getResponseCode()}
    {assign var="headers" value=$response->getHeaders()}

    <p class="response">
        <span class="responseCode{if $responseCode == "200"} success{elseif $response && $response->isRedirect()} redirect{else} failure{/if}">{$responseCode}{if $responseCode != 0} {"spider.label.response.`$responseCode`"|translate}{/if}</span>
    </p>
    <p class="url">
        <a href="{$node->getUrl()}" target="_blank">{$node->getUrl()}</a>
    </p
    
    {if $headers}
    <h4>{"spider.label.headers"|translate}</h4>
    <table class="headers">
    {foreach from=$headers item="value" key="key"}
        <tr class="{cycle values="odd,even"}">
            <td class="header">{$key}</td>
            <td>{$value}</td>
        </tr>
    {/foreach}
    </table>
    {/if}
{else}    
    <p class="url">
        <a href="{$node->getUrl()}" target="_blank">{$node->getUrl()}</a>
    </p
{/if}

<div class="clear">
    <div class="links">
        <h4>{"spider.label.links"|translate}</h4>
{if $links}
        <ul>
    {foreach from=$links item="link"}
            <li>{$link->getUrl()}</li>
    {/foreach}
        </ul>
{else}
        <p>{"spider.label.links.none"|translate}</p>
{/if}
    </div>

    <div class="references">
        <h4>{"spider.label.references"|translate}</h4>
{if $references}
        <ul>
    {foreach from=$references item="reference"}
            <li>{$reference->getUrl()}</li>
    {/foreach}
        </ul>
{else}
        <p>{"spider.label.references.none"|translate}</p>
{/if}
    </div>
</div>