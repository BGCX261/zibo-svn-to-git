{form form=$form}
    <p>{"joppa.security.form.new"|translate}</p>

    <div class="password">
        <label for="{fieldId form=$form name="password"}">{"joppa.security.label.password.new"|translate}</label>
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