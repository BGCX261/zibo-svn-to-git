{if $node}
    <h2>{"joppa.title.site.edit"|translate}: <span>{$node->name}</span></h2>
{else}
    <h2>{"joppa.title.site.add"|translate}</h2>
{/if}

{form form=$form}
    {field form=$form name="id"}
    {field form=$form name="version"}
    {fieldErrors form=$form name="version"}
    
    {field form=$form name="siteId"}
    {field form=$form name="siteVersion"}
    {fieldErrors form=$form name="siteVersion"}
    
    <div class="name">
        <label for="{fieldId form=$form name="name"}">{"joppa.label.site"|translate}</label>
        <span>{"joppa.label.node.name.description"|translate}</span>
        {field form=$form name="name"}
        {fieldErrors form=$form name="name"}
    </div>
    
    <div class="metaDescription">
        <label for="{fieldId form=$form name="metaDescription"}">{"joppa.label.meta.description"|translate}</label>
        <span>{"joppa.label.meta.description.description"|translate}</span>
        {field form=$form name="metaDescription"}
        {fieldErrors form=$form name="metaDescription"}
    </div>

    <div class="metaKeywords">
        <label for="{fieldId form=$form name="metaKeywords"}">{"joppa.label.meta.keywords"|translate}</label>
        <span>{"joppa.label.meta.keywords.description"|translate}</span>
        {field form=$form name="metaKeywords"}
        {fieldErrors form=$form name="metaKeywords"}
    </div>

    <div class="theme">
        <label for="{fieldId form=$form name="theme"}">{"joppa.label.theme"|translate}</label>
        {field form=$form name="theme"}
        {fieldErrors form=$form name="theme"}
    </div>

{if $node}
    <div class="defaultNode">
        <label for="{fieldId form=$form name="defaultNode"}">{"joppa.label.node.default"|translate}</label>
        <span>{"joppa.label.node.default.description"|translate}</span>
        {field form=$form name="defaultNode"}
        {fieldErrors form=$form name="defaultNode"}
    </div>
{/if}    

    <div class="locales">
        <label for="{fieldId form=$form name="locales"}">{"joppa.label.locales"|translate}</label>
        <span>{"joppa.label.locales.description"|translate}</span>
        {field form=$form name="locales"}
        {fieldErrors form=$form name="locales"}
    </div>

    <div class="localizationMethod">
        <label for="{fieldId form=$form name="localizationMethod"}">{"joppa.label.localization.method"|translate}</label>
        <span>{"joppa.label.localization.method.description"|translate}</span>
        {field form=$form name="localizationMethod"}
        {fieldErrors form=$form name="localizationMethod"}
    </div>

    <div class="baseUrl">
        <label for="{fieldId form=$form name="baseUrl"}">{"joppa.label.url.base"|translate}</label>
        <span>{"joppa.label.url.base.description"|translate}</span>
        {field form=$form name="baseUrl"}
        {fieldErrors form=$form name="baseUrl"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}