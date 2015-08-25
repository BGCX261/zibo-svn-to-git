<div class="fieldName">
    <label for="{fieldId form=$wizard name="fieldName"}">{"orm.label.field.name"|translate}</label>
    {if !$isFieldNameRequired}
        <span>{"orm.label.field.name.not.required"|translate}</span>    
    {/if}
    {field form=$wizard name="fieldName"}
    {fieldErrors form=$wizard name="fieldName"}
</div>

<div class="fieldLabel">
    <label for="{fieldId form=$wizard name="fieldLabel"}">{"orm.label.field.label"|translate}</label>
    <span>{"orm.label.field.label.description"|translate}</span>
    {field form=$wizard name="fieldLabel"}
    {fieldErrors form=$wizard name="fieldLabel"}
</div>

<div class="isLocalized">
    <label for="{fieldId form=$wizard name="isLocalized"}">{"orm.label.field.localize"|translate}</label>
    <span>{"orm.label.field.localize.description"|translate}</span>
    {field form=$wizard name="isLocalized"}
    {fieldErrors form=$wizard name="isLocalized"}
</div>

<div class="fieldTypeProperty">
    {field form=$wizard name="fieldType" option="property"}
    {fieldErrors form=$wizard name="fieldType"}
</div>

<div class="property">
    <div class="propertyType">
        <label for="{fieldId form=$wizard name="propertyType"}">{"orm.label.type"|translate}</label>
        {field form=$wizard name="propertyType"}
        {fieldErrors form=$wizard name="propertyType"}
    </div>

    <div class="propertyDefault">
        <label for="{fieldId form=$wizard name="propertyDefault"}">{"orm.label.value.default"|translate}</label>
        {field form=$wizard name="propertyDefault"}
        {fieldErrors form=$wizard name="propertyDefault"}
    </div>
</div>

<div class="fieldTypeRelation">
    {field form=$wizard name="fieldType" option="relation"}
    {fieldErrors form=$wizard name="fieldType"}
</div>

<div class="relation">
    <div class="relationType">
        <label for="{fieldId form=$wizard name="relationType"}">{"orm.label.type"|translate}</label>
        {field form=$wizard name="relationType"}
        {fieldErrors form=$wizard name="relationType"}
    </div>

    <div class="relationModel">
        <label for="{fieldId form=$wizard name="relationModel"}">{"orm.label.model"|translate}</label>
        {field form=$wizard name="relationModel"}
        {fieldErrors form=$wizard name="relationModel"}
    </div>
    
    <div class="relationIsDependant">
        <label for="{fieldId form=$wizard name="relationIsDependant"}">{"orm.label.relation.dependant"|translate}</label>
        <span>{"orm.label.relation.dependant.description"|translate}</span>
        {field form=$wizard name="relationIsDependant"}
        {fieldErrors form=$wizard name="relationIsDependant"}
    </div>

    <div class="relationAdvanced">
        <p><a href="#" id="advancedAnchor">{"button.advanced"|translate}</a></p>
    
        <div class="advanced">
            <div class="relationLinkModel">
                <label for="{fieldId form=$wizard name="relationLinkModel"}">{"orm.label.model.link"|translate}</label>
                <span>{"orm.label.model.link.description"|translate}</span>
                {field form=$wizard name="relationLinkModel"}
                {fieldErrors form=$wizard name="relationLinkModel"}
            </div>
        
            <div class="relationForeignKey">
                <label for="{fieldId form=$wizard name="relationForeignKey"}">{"orm.label.relation.fk"|translate}</label>
                <span>{"orm.label.relation.fk.description"|translate}</span>
                {field form=$wizard name="relationForeignKey"}
                {fieldErrors form=$wizard name="relationForeignKey"}
            </div>
            
            <div class="relationOrder">
                <label for="{fieldId form=$wizard name="relationOrder"}">{"orm.label.relation.order"|translate}</label>
                <span>{"orm.label.relation.order.description"|translate}</span>
                {field form=$wizard name="relationOrder"}
                {fieldErrors form=$wizard name="relationOrder"}
            </div>
        </div>
    </div>

</div>