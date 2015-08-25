{form form=$form}
    <p>{"joppa.security.form.register"|translate}</p>
    
    <div class="username">
        <label for="{fieldId form=$form name="username"}">{"joppa.security.label.username"|translate}</label>
        {field form=$form name="username"}
        {fieldErrors form=$form name="username"}
    </div>

    <div class="email">
        <label for="{fieldId form=$form name="email"}">{"joppa.security.label.email"|translate}</label>
        {field form=$form name="email"}
        {fieldErrors form=$form name="email"}
    </div>

    <div class="password">
        <label for="{fieldId form=$form name="password"}">{"joppa.security.label.password"|translate}</label>
        {field form=$form name="password"}
        {fieldErrors form=$form name="password"}
    </div>

    <div class="passwordConfirm">
        <label for="{fieldId form=$form name="passwordConfirm"}">{"joppa.security.label.password.confirm"|translate}</label>
        {field form=$form name="passwordConfirm"}
        {fieldErrors form=$form name="passwordConfirm"}
    </div>

    <div class="submit">
        {field form=$form name="submit"}
    </div>
{/form}