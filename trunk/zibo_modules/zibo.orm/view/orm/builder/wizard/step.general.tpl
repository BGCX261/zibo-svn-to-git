<div class="modelName">
    <label for="{fieldId form=$wizard name="modelName"}">{"orm.label.model.name"|translate}</label>
    {field form=$wizard name="modelName"}
    {fieldErrors form=$wizard name="modelName"}
</div>

<div class="isLogged">
    <label for="{fieldId form=$wizard name="isLogged"}">{"orm.label.model.log"|translate}</label>
    <span>{"orm.label.model.log.description"|translate}</span>
    {field form=$wizard name="isLogged"}
    {fieldErrors form=$wizard name="isLogged"}
</div>

<p><a href="#" id="advancedAnchor">{"button.advanced"|translate}</a></p>

<div class="advanced">
    <div class="willBlockDelete">
        <label for="{fieldId form=$wizard name="willBlockDelete"}">{"orm.label.model.block.delete"|translate}</label>
        <span>{"orm.label.model.block.delete.description"|translate}</span>
        {field form=$wizard name="willBlockDelete"}
        {fieldErrors form=$wizard name="willBlockDelete"}
    </div>

    <div class="modelClass">
        <label for="{fieldId form=$wizard name="modelClass"}">{"orm.label.model.class"|translate}</label>
        <span>{"orm.label.model.class.description"|translate}</span>
        {field form=$wizard name="modelClass"}
        {fieldErrors form=$wizard name="modelClass"}
    </div>

    <div class="dataClass">
        <label for="{fieldId form=$wizard name="dataClass"}">{"orm.label.data.class"|translate}</label>
        <span>{"orm.label.data.class.description"|translate}</span>
        {field form=$wizard name="dataClass"}
        {fieldErrors form=$wizard name="dataClass"}
    </div>
</div>