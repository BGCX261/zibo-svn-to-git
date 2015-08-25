{form form=$form}
    <div class="label">
        <label for="{fieldId form=$form name="label"}">{"joppa.widget.breadcrumbs.label.label"|translate}</label>
        <span>{"joppa.widget.breadcrumbs.label.label.description"|translate}</span>
        {field form=$form name="label"}
        {fieldErrors form=$form name="label"}
    </div>
    
    <div class="filter">
        <label for="{fieldId form=$form name="filter"}">{"joppa.widget.breadcrumbs.label.filter"|translate}</label>
        <span>{"joppa.widget.breadcrumbs.label.filter.description"|translate}</span>
        <span>{"label.multiselect"|translate}</span>
        {field form=$form name="filter"}
        {fieldErrors form=$form name="filter"}
    </div>
    
    <div class="styleId">
        <label for="{fieldId form=$form name="styleId"}">{"joppa.widget.breadcrumbs.label.style.id"|translate}</label>
        <span>{"joppa.widget.breadcrumbs.label.style.id.description"|translate}</span>
        {field form=$form name="styleId"}
        {fieldErrors form=$form name="styleId"}
    </div>    
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}