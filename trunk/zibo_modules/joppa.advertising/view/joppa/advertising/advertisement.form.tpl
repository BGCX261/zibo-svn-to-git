<h2>{$title}</h2>

{form form=$form}
    {field form=$form name="id"}
    {fieldErrors form=$form name="id"}
    {field form=$form name="version"}
    {fieldErrors form=$form name="version"}
    
    <div class="name">
        <label for="{fieldId form=$form name="name"}">{"joppa.advertising.label.name"|translate}</label>
        <span>{"joppa.advertising.label.name.description"|translate}</span>
        {field form=$form name="name"}
        {fieldErrors form=$form name="name"}
    </div>

    <div class="website">
        <label for="{fieldId form=$form name="website"}">{"joppa.advertising.label.website"|translate}</label>
        <span>{"joppa.advertising.label.website.description"|translate}</span>
        {field form=$form name="website"}
        {fieldErrors form=$form name="website"}
    </div>

    <div class="image">
        <label for="{fieldId form=$form name="image"}">{"joppa.advertising.label.image"|translate}</label>
        {field form=$form name="image"}
        {fieldErrors form=$form name="image"}
    </div>

    <div class="dateStart">
        <label for="{fieldId form=$form name="dateStart"}">{"joppa.advertising.label.date.start"|translate}</label>
        {field form=$form name="dateStart"}
        {fieldErrors form=$form name="dateStart"}
    </div>

    <div class="dateStop">
        <label for="{fieldId form=$form name="dateStop"}">{"joppa.advertising.label.date.stop"|translate}</label>
        {field form=$form name="dateStop"}
        {fieldErrors form=$form name="dateStop"}
    </div>

    <div class="blocks">
        <label for="{fieldId form=$form name="blocks"}">{"joppa.advertising.label.blocks"|translate}</label>
        <span>{"joppa.advertising.label.blocks.description"|translate}</span>
        <span>{"label.multiselect"|translate}</span>
        {field form=$form name="blocks"}
        {fieldErrors form=$form name="blocks"}
    </div>

{if $advertisement}
    <div class="clicks">
        {translate key="joppa.advertising.label.clicks" clicks=$advertisement->clicks}
    </div>
{/if}
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}
