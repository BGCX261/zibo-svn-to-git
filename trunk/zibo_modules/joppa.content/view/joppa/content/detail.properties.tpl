{form form=$form}
    <div id="{$form->getId()}Tabs" class="tabs">
        <ul>
            <li><a href="#tabQuery">{"joppa.content.title.query"|translate}</a></li>
            <li><a href="#tabParameters">{"joppa.content.title.parameters"|translate}</a></li>
            <li><a href="#tabView">{"joppa.content.title.view"|translate}</a></li>
        </ul>
        
        <div id="tabQuery">
            <div class="model">
                <label for="{fieldId form=$form name="model"}">{"joppa.content.label.model"|translate}</label>
                <span>{"joppa.content.label.model.description"|translate}</span>
                {field form=$form name="model"}
                {fieldErrors form=$form name="model"}
            </div>
        
            <div class="fields">
                <label for="{fieldId form=$form name="fields"}">{"joppa.content.label.fields"|translate}</label>
                <span>{"joppa.content.label.fields.description"|translate}</span>
                <span>{"label.multiselect"|translate}</span>
                {field form=$form name="fields"}
                {fieldErrors form=$form name="fields"}
            </div>
        
            <div class="recursiveDepth">
                <label for="{fieldId form=$form name="recursiveDepth"}">{"joppa.content.label.depth"|translate}</label>
                <span>{"joppa.content.label.depth.description"|translate}</span>
                {field form=$form name="recursiveDepth"}
                {fieldErrors form=$form name="recursiveDepth"}
            </div>

            <div class="includeUnlocalized">
                <label for="{fieldId form=$form name="includeUnlocalized"}">{"joppa.content.label.unlocalized"|translate}</label>
                <span>{"joppa.content.label.unlocalized.description"|translate}</span>
                {field form=$form name="includeUnlocalized"}
                {fieldErrors form=$form name="includeUnlocalized"}
            </div>
        </div>       
        
        <div id="tabParameters">
            <div class="parameterIdId">
                {field form=$form name="parameterId" option="id"}
                <span>{"joppa.content.label.primary.key.description"|translate}</span>
            </div>

            <div class="parameterIdSlug">
                {field form=$form name="parameterId" option="slug"}
                <span>{"joppa.content.label.slug.description"|translate}</span>
            </div>
        </div>
        
        <div id="tabView">           
            <div class="view">
                <label for="{fieldId form=$form name="view"}">{"joppa.content.label.view"|translate}</label>
                <span>{"joppa.content.label.view.description"|translate}</span>
                {field form=$form name="view"}
                {fieldErrors form=$form name="view"}
            </div>
        </div>
    </div>

    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>

{/form}