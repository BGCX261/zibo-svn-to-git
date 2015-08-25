<div class="username">
    <label>{"security.label.username"|translate}</label>
    <span>{"security.label.username.description"|translate}</span>
    <strong>{$user->getUserName()}</strong>
</div>
<div class="email">
    <label for="{fieldId form=$form name="email"}">{"security.label.email"|translate}</label>
    <span>{"security.label.email.description"|translate}</span>
    {field form=$form name="email"}
    {fieldErrors form=$form name="email"}
</div>

<div class="submit">
    {field form=$form name="submitEmail"}
</div>

<hr />

<div class="password">
    <label for="{fieldId form=$form name="newPassword"}">{"security.label.password.new"|translate}</label>
    {field form=$form name="newPassword"}
    {fieldErrors form=$form name="newPassword"}
</div>

<div class="password">
    <label for="{fieldId form=$form name="newPasswordConfirm"}">{"security.label.password.confirm"|translate}</label>
    <span>{"security.label.password.confirm.description"|translate}</span>
    {field form=$form name="newPasswordConfirm"}
    {fieldErrors form=$form name="newPasswordConfirm"}
</div>

<div class="submit">
    {field form=$form name="submitPassword"}
</div>