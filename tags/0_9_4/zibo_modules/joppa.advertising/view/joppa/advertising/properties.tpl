{form form=$form}
    {field form=$form name="id"}
    {fieldErrors form=$form name="id"}
    {field form=$form name="version"}
    {fieldErrors form=$form name="version"}
    
    <div class="name">
        <label for="{fieldId form=$form name="name"}">{"joppa.advertising.label.block.name"|translate}</label>
        <span>{"joppa.advertising.label.block.name.description"|translate}</span>
        {field form=$form name="name"}
        {fieldErrors form=$form name="name"}
    </div>

    <div class="width">
        <label for="{fieldId form=$form name="width"}">{"joppa.advertising.label.width"|translate}</label>
        <span>{"joppa.advertising.label.width.description"|translate}</span>
        {field form=$form name="width"}
        {fieldErrors form=$form name="width"}
    </div>

    <div class="height">
        <label for="{fieldId form=$form name="height"}">{"joppa.advertising.label.height"|translate}</label>
        <span>{"joppa.advertising.label.height.description"|translate}</span>
        {field form=$form name="height"}
        {fieldErrors form=$form name="height"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}
