<table>
    <tr>
        <th class="o,dex">#</th>
        <th>{"spider.label.url"|translate}</th>
        <th>{"spider.label.response"|translate}</th>
    </tr>
{if $nodes}
    {foreach from=$nodes item="node" name="nodes"}
        {assign var="response" value=$node->getResponse()}
        {if $response}
            {assign var="responseCode" value=$response->getResponseCode()}
        {else}
            {assign var="responseCode" value="---"}
        {/if}
        
        {assign var="links" value=$node->getLinks()}
        {assign var="references" value=$node->getReferences()}
        
        {cycle values="odd,even" assign="rowClass"}
    
    <tr class="{$rowClass}">
        <td class="index">{$smarty.foreach.nodes.index+1}</td>
        <td class="url"><a href="{$node->getUrl()}" target="_blank">{$node->getUrl()}</a></td>
        <td class="responseCode{if $responseCode == "200"} success{elseif $response && $response->isRedirect()} redirect{else} failure{/if}">{$responseCode}{if $responseCode != 0} {"spider.label.response.`$responseCode`"|translate}{/if}</td>
    </tr>
    {if $node->getError()}
    <tr class="{$rowClass}">
        <td class="index"></td>
        <td class="error" colspan="2">{$node->getError()}</td>
    </tr>    
    {/if}
    {/foreach}
{/if}
</table>