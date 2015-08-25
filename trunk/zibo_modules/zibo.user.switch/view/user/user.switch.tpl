<h2>{"user.title.switch"|translate}</h2>

<p>{"user.label.switch.description"|translate}</p>

{form form=$form}
    <div class="username">
        <label for="{fieldId form=$form name="username"}">{"security.label.username"|translate}</label>
        {field form=$form name="username"}
        {fieldErrors form=$form name="username"}
    </div>
    <div class="submit">
        {field form=$form name="submit"}
    </div>
{/form}