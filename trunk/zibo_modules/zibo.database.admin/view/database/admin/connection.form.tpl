<h2>{$title|translate}</h2>

{form form=$form}
    {field form=$form name="oldName"}
    {fieldErrors form=$form name="oldName"}

    <div class="name">
        <label for="{fieldId form=$form name="name"}">{"database.label.name"|translate}</label>
        <span>{"database.label.name.description"|translate}</span>
        {field form=$form name="name"}
        {fieldErrors form=$form name="name"}
    </div>

    <div class="dsn">
        <label for="{fieldId form=$form name="dsn"}">{"database.label.dsn"|translate}</label>
        <span>{"database.label.dsn.description"|translate}</span>
        {field form=$form name="dsn"}
        {fieldErrors form=$form name="dsn"}
    </div>

    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>

{/form}