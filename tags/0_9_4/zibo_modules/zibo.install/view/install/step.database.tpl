<h2>{"install.title.database"|translate}</h2>

<p>{"install.label.database"|translate}</p>

{fieldErrors name="dsn" form=$wizard}

<div class="question clear">
    <label for="{fieldId name="protocol" form=$wizard}">{"install.label.database.protocol"|translate}</label>
    {field name="protocol" form=$wizard}
    {fieldErrors name="protocol" form=$wizard}
</div>

<div class="question clear">
    <label for="{fieldId name="server" form=$wizard}">{"install.label.database.server"|translate}</label>
    {field name="server" form=$wizard}
    {fieldErrors name="server" form=$wizard}
</div>

<div class="question clear">
    <label for="{fieldId name="port" form=$wizard}">{"install.label.database.port"|translate}</label>
    {field name="port" form=$wizard}
    {fieldErrors name="port" form=$wizard}
</div>

<div class="question clear">
    <label for="{fieldId name="database" form=$wizard}">{"install.label.database.database"|translate}</label>
    {field name="database" form=$wizard}
    {fieldErrors name="database" form=$wizard}
</div>

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