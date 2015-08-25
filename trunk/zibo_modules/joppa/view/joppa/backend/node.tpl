{if $node && $node->id}
    <h2>{"joppa.title.node.edit"|translate}: <span>{$node->name}</span></h2>
{else}
    <h2>{"joppa.title.node.add"|translate}</h2>
{/if}

{include file="joppa/backend/node.form"}