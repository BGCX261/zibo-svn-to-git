<h2>{"install.title.welcome"|translate}</h2>

<p>{"install.label.intro"|translate}</p>

<div class="question clear">
    <label for="{fieldId name="profile" form=$wizard}">{"install.label.profile.select"|translate}</label>
    {field name="profile" form=$wizard}
    {fieldErrors name="profile" form=$wizard}
</div>