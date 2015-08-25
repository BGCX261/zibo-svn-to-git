{if !$form}

<p>{"joppa.widget.search.warning.properties.unset"|translate}</p>

{else}

{form form=$form}
    {field form=$form name="query"}
    {field form=$form name="search"}
{/form}

{/if}