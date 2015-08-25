{if $node}
    <h2>{"joppa.title.page.edit"|translate}: <span>{$node->name}</span></h2>
{else}
    <h2>{"joppa.title.page.add"|translate}</h2>
{/if}

{include file="joppa/backend/node.form" form=$form}