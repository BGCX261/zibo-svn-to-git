<h3>{"orm.title.filter"|translate}</h3>

{form form=$filterForm}
    <div class="includeCustomModels">
        {field form=$filterForm name="include" option="custom"}
    </div>

    <div class="includeModuleModels">
        {field form=$filterForm name="include" option="module"}
    </div>

    <div class="includeLocalizedModels">
        {field form=$filterForm name="include" option="localized"}
    </div>

    <div class="includeLinkModels">
        {field form=$filterForm name="include" option="link"}
    </div>

    <div class="submit">
        {field form=$filterForm name="submit"}
    </div>
{/form}