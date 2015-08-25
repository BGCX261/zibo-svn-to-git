{form form=$form}
    {field form=$form name="locale"}
    {fieldErrors form=$form name="locale"}

    <div class="content">
        <label for="{fieldId form=$form name="content"}">{"widget.html.label.html"|translate}</label>
        <span>{"widget.html.label.html.description"|translate}
        {field form=$form name="content"}
        {fieldErrors form=$form name="name"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}