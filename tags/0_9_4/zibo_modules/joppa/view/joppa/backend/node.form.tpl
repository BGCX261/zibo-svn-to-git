{form form=$form}
    {field form=$form name="id"}
    {field form=$form name="version"}
    {fieldErrors form=$form name="version"}
    
    <div class="name">
        {assign var="nodeType" value=$form->getNodeType()|default:"node"}
        <label for="{fieldId form=$form name="name"}">{"joppa.label.`$nodeType`"|translate}</label>
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

    <div class="route">
        <label for="{fieldId form=$form name="route"}">{"joppa.label.route"|translate}</label>
        <span>{"joppa.label.route.description"|translate}</span>
        {field form=$form name="route"}
        {fieldErrors form=$form name="route"}
    </div>

    <div class="parent">
        <label for="{fieldId form=$form name="parent"}">{"joppa.label.parent"|translate}</label>
        <span>{"joppa.label.parent.description"|translate}</span>
        {field form=$form name="parent"}
        {fieldErrors form=$form name="parent"}
    </div>

    <div class="locales">
        <label for="{fieldId form=$form name="locales"}">{"joppa.label.locales"|translate}</label>
        <span>{"joppa.label.locales.description"|translate}</span>
        {field form=$form name="locales"}
        {fieldErrors form=$form name="locales"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}