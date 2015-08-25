<div class="predefined">
    <label for="{fieldId form=$wizard name="predefined"}">{"orm.label.format.predefined"|translate}</label>
    {field form=$wizard name="predefined"}
</div>

<div class="name">
    <label for="{fieldId form=$wizard name="name"}">{"orm.label.format.name"|translate}</label>
    {field form=$wizard name="name"}
    {fieldErrors form=$wizard name="name"}
</div>

<div class="format">
    <label for="{fieldId form=$wizard name="format"}">{"orm.label.format"|translate}</label>
    <span>{"orm.label.format.description"|translate}</span>
    {field form=$wizard name="format"}
    {fieldErrors form=$wizard name="format"}
</div>

<div class="submit">
    {field form=$wizard name="add"}
</div>

{include file="helper/table" table=$formatTable}