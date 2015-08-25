{form form=$form}
    <div class="level">
        <label for="{fieldId form=$form name="level"}">{"joppa.widget.title.label.level"|translate}</label>
        <span>{"joppa.widget.title.label.level.description"|translate}</span>
        {field form=$form name="level"}
        {fieldErrors form=$form name="level"}
    </div>

    <div class="styleClass">
        <label for="{fieldId form=$form name="styleClass"}">{"joppa.widget.title.label.style.class"|translate}</label>
        <span>{"joppa.widget.title.label.style.class.description"|translate}</span>
        {field form=$form name="styleClass"}
        {fieldErrors form=$form name="styleClass"}
    </div>

    <div class="styleId">
        <label for="{fieldId form=$form name="styleId"}">{"joppa.widget.title.label.style.id"|translate}</label>
        <span>{"joppa.widget.title.label.style.id.description"|translate}</span>
        {field form=$form name="styleId"}
        {fieldErrors form=$form name="styleId"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}