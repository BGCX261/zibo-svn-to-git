<h2>{"install.title.security"|translate}</h2>

<p>{"install.label.security"|translate}</p>

<div class="question clear">
    <label for="{fieldId name="username" form=$wizard}">{"install.label.username"|translate}</label>
    {field name="username" form=$wizard}
    {fieldErrors name="username" form=$wizard}
</div>

<div class="question clear">
    <label for="{fieldId name="password" form=$wizard}">{"install.label.password"|translate}</label>
    {field name="password" form=$wizard}
    {fieldErrors name="password" form=$wizard}
</div>

<div class="question clear">
    <label for="{fieldId name="email" form=$wizard}">{"install.label.email"|translate}</label>
    {field name="email" form=$wizard}
    {fieldErrors name="email" form=$wizard}
</div>