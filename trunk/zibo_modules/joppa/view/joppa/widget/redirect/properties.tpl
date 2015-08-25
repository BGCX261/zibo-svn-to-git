{form form=$form}
    <div class="redirectTypeNode option">
        {field form=$form name="redirectType" option="node"}
    </div>
    
    <div class="node optionField">
        <label for="{fieldId form=$form name="node"}">{"joppa.widget.redirect.label.node"|translate}</label>
        <span>{"joppa.widget.redirect.label.node.description"|translate}</span>
        {field form=$form name="node"}
        {fieldErrors form=$form name="node"}
    </div>
    
    <div class="redirectTypeUrl option">
        {field form=$form name="redirectType" option="url"}
    </div>
    
    <div class="url optionField">
        <label for="{fieldId form=$form name="url"}">{"joppa.widget.redirect.label.url"|translate}</label>
        <span>{"joppa.widget.redirect.label.url.description"|translate}</span>
        {field form=$form name="url"}
        {fieldErrors form=$form name="url"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}