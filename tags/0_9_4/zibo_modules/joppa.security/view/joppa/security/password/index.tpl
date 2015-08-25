{form form=$form}
    <p>{"joppa.security.form.reset"|translate}</p>
    
    <div class="username">
        <label for="{fieldId form=$form name="username"}">{"joppa.security.label.username"|translate}</label>
        {field form=$form name="username"}
        {fieldErrors form=$form name="username"}
    </div>

    <div class="or">{"joppa.security.label.or"|translate}</div>

    <div class="email">
        <label for="{fieldId form=$form name="email"}">{"joppa.security.label.email"|translate}</label>
        {field form=$form name="email"}
        {fieldErrors form=$form name="email"}
    </div>

    <div class="submit">
        {field form=$form name="submit"}
    </div>
{/form}