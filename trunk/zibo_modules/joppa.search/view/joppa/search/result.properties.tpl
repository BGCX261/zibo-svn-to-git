{form form=$form}
    <div class="types">
        <label for="{fieldId form=$form name="types"}">{"joppa.search.label.types"|translate}</label>
        <span>{"joppa.search.label.types.description"|translate}</span>
        <span>{"label.multiselect"|translate}</span>
        {field form=$form name="types"}
        {fieldErrors form=$form name="types"}
    </div>
    
    <div class="submit">
        {field form=$form name="save"}
        {field form=$form name="cancel"}
    </div>
{/form}