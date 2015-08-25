<div class="indexName">
    <label for="{fieldId form=$wizard name="indexName"}">{"orm.label.index.name"|translate}</label>
    {field form=$wizard name="indexName"}
    {fieldErrors form=$wizard name="indexName"}
</div>

<div class="indexFields">
    <label for="{fieldId form=$wizard name="indexFields"}">{"orm.label.index.fields"|translate}</label>
    {field form=$wizard name="indexFields"}
    {fieldErrors form=$wizard name="indexFields"}
</div>

<div class="submit">
    {field form=$wizard name="add"}
</div>

{include file="helper/table" table=$indexTable}