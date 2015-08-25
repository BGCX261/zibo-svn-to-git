{assign var="prefix" value="`$prefix``$number`"}

<ul id="{$prefix}">
{foreach from=$items item="node" name="nodes"}
	{if $node->isPublished() && $node->isAllowed() && $node->isAvailableInLocale()}
    <li class="{if $smarty.foreach.nodes.first}first {/if}{cycle values="even,odd" name=$prefix}{if $currentNode->hasParentNode($node)} activeTrail{elseif $currentNode->id == $node->id} active{/if}{if $smarty.foreach.nodes.last} last{/if}">
        <a href="{$_baseUrl}/{$node->getRoute()}">{$node->name}</a>
        {if $node->children}
            {include file="joppa/widget/menu/menu.nested" items=$node->children prefix="`$prefix`sub"}
            {assign var="number" value=$number+1}
        {/if}
    </li>
    {/if}
{/foreach}
</ul>