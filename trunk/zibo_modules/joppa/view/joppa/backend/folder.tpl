{if $node}
    <h2>{"joppa.title.folder.edit"|translate}: <span>{$node->name}</span></h2>
{else}
    <h2>{"joppa.title.folder.add"|translate}</h2>
{/if}

{form form=$form}
    {field form=$form name="id"}
    {field form=$form name="version"}
    {fieldErrors form=$form name="version"}
    
    <div class="name">
        <label for="{fieldId form=$form name="name"}">{"joppa.label.folder"|translate}</label>
        {field form=$form name="name"}
        {fieldErrors form=$form name="name"}
    </div>

    <div class="theme">
        <label for="{fieldId form=$form name="theme"}">{"joppa.label.theme"|translate}</label>
        {field form=$form name="theme"}
        {fieldErrors form=$form name="theme"}
    </div>

    <div class="parent">
        <label for="{fieldId form=$form name="parent"}">{"joppa.label.parent"|translate}</label>
        {field form=$form name="parent"}
        {fieldErrors form=$form name="parent"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}