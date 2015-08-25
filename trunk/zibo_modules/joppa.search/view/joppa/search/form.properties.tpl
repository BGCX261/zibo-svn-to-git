{form form=$form}
    <div class="Node">
        <label for="{fieldId form=$form name="node"}">{"joppa.search.label.node.result"|translate}</label>
        <span>{"joppa.search.label.node.result.description"|translate}</span>
        {field form=$form name="node"}
        {fieldErrors form=$form name="node"}
    </div>
    
    <div class="submit">
        {field form=$form name="save"}
        {field form=$form name="cancel"}
    </div>
{/form}