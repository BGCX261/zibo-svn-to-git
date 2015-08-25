<h3>{"orm.title.filter"|translate}</h3>

{form form=$filterForm}
    <div>
        <label for="{fieldId form=$filterForm name="include"}">{"orm.label.actions"|translate}</label>
        <span>{"orm.label.filter.actions.description"|translate}</span>
    </div>
    
    <div class="includeInsert option">
        {field form=$filterForm name="include" option="insert"}
    </div>

    <div class="includeModuleModels option">
        {field form=$filterForm name="include" option="update"}
    </div>

    <div class="includeLocalizedModels option">
        {field form=$filterForm name="include" option="delete"}
    </div>

    <div class="dataModel">
        <label for="{fieldId form=$filterForm name="dataModel"}">{"orm.label.data.model"|translate}</label>
        <span>{"orm.label.data.model.description"|translate}</span>
        {field form=$filterForm name="dataModel"}
    </div>

    <div class="dataId">
        <label for="{fieldId form=$filterForm name="dataId"}">{"orm.label.data.id"|translate}</label>
        <span>{"orm.label.data.id.description"|translate}</span>
        {field form=$filterForm name="dataId"}
    </div>

    <div class="dataField">
        <label for="{fieldId form=$filterForm name="dataField"}">{"orm.label.data.field"|translate}</label>
        <span>{"orm.label.data.field.description"|translate}</span>
        {field form=$filterForm name="dataField"}
    </div>

    <div class="submit">
        {field form=$filterForm name="submit"}
    </div>
{/form}