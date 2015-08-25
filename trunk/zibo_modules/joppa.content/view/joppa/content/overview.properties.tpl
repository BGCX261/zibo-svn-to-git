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
        
            <h4>{"joppa.content.title.condition"|translate}</h4>
            
            <div class="conditionExpression">
                <label for="{fieldId form=$form name="conditionExpression"}">{"joppa.content.label.condition.expression"|translate}</label>
                <span>{"joppa.content.label.condition.expression.description"|translate}</span>
                {field form=$form name="conditionExpression"}
                {fieldErrors form=$form name="conditionExpression"}
            </div>
            
            <h4>{"joppa.content.title.order"|translate}</h4>
            
            <div class="orderField">
                <label for="{fieldId form=$form name="orderField"}">{"joppa.content.label.order.field"|translate}</label>
                <span>{"joppa.content.label.order.field.description"|translate}</span>
                {field form=$form name="orderField"}
                {field form=$form name="orderDirection"}
                {field form=$form name="orderAdd"}
            </div>

            <div class="orderExpression">
                <label for="{fieldId form=$form name="orderExpression"}">{"joppa.content.label.order.expression"|translate}</label>
                <span>{"joppa.content.label.order.expression.description"|translate}</span>
                {field form=$form name="orderExpression"}
                {fieldErrors form=$form name="orderExpression"}
            </div>

            <h4>{"joppa.content.title.pagination"|translate}</h4>
        
            <div class="paginationEnable">
                <label for="{fieldId form=$form name="paginationEnable"}">{"joppa.content.label.pagination.enable"|translate}</label>
                <span>{"joppa.content.label.pagination.enable.description"|translate}</span>
                {field form=$form name="paginationEnable"}
                {fieldErrors form=$form name="paginationEnable"}
            </div>
        
            <div class="paginationRows paginationAttribute">
                <label for="{fieldId form=$form name="paginationRows"}">{"joppa.content.label.pagination.rows"|translate}</label>
                <span>{"joppa.content.label.pagination.rows.description"|translate}</span>
                {field form=$form name="paginationRows"}
                {fieldErrors form=$form name="paginationRows"}
            </div>
        
            <div class="paginationOffset paginationAttribute">
                <label for="{fieldId form=$form name="paginationOffset"}">{"joppa.content.label.pagination.offset"|translate}</label>
                <span>{"joppa.content.label.pagination.offset.description"|translate}</span>
                {field form=$form name="paginationOffset"}
                {fieldErrors form=$form name="paginationOffset"}
            </div>
        </div>       
        
        <div id="tabParameters">
            <div class="parametersTypeNone">
                {field form=$form name="parametersType" option="none"}
            </div>

            <div class="parametersTypeNumeric">
                {field form=$form name="parametersType" option="numeric"}
            </div>

            <div class="parametersTypeNamed">
                {field form=$form name="parametersType" option="named"}
            </div>
        </div> 
            
        <div id="tabView">           
            <div class="view">
                <label for="{fieldId form=$form name="view"}">{"joppa.content.label.view"|translate}</label>
                <span>{"joppa.content.label.view.description"|translate}</span>
                {field form=$form name="view"}
                {fieldErrors form=$form name="view"}
            </div>

            <div class="contentTitleFormat">
                <label for="{fieldId form=$form name="contentTitleFormat"}">{"joppa.content.label.format.title"|translate}</label>
                <span>{"joppa.content.label.format.title.description"|translate}</span>
                {field form=$form name="contentTitleFormat"}
                {fieldErrors form=$form name="contentTitleFormat"}
            </div>

            <div class="contentTeaserFormat">
                <label for="{fieldId form=$form name="contentTeaserFormat"}">{"joppa.content.label.format.teaser"|translate}</label>
                <span>{"joppa.content.label.format.teaser.description"|translate}</span>
                {field form=$form name="contentTeaserFormat"}
                {fieldErrors form=$form name="contentTeaserFormat"}
            </div>
            
            <div class="title">
                <label for="{fieldId form=$form name="title"}">{"joppa.content.label.title"|translate}</label>
                <span>{"joppa.content.label.title.description"|translate}</span>
                {field form=$form name="title"}
                {fieldErrors form=$form name="title"}
            </div>

            <div class="emptyResultMessage">
                <label for="{fieldId form=$form name="emptyResultMessage"}">{"joppa.content.label.message.result.empty"|translate}</label>
                <span>{"joppa.content.label.message.result.empty.description"|translate}</span>
                {field form=$form name="emptyResultMessage"}
                {fieldErrors form=$form name="emptyResultMessage"}
            </div>
            
            <h4 class="paginationAttribute">{"joppa.content.title.pagination"|translate}</h4>

            <div class="paginationShow paginationAttribute">
                <label for="{fieldId form=$form name="paginationShow"}">{"joppa.content.label.pagination.show"|translate}</label>
                <span>{"joppa.content.label.pagination.show.description"|translate}</span>
                {field form=$form name="paginationShow"}
                {fieldErrors form=$form name="paginationShow"}
            </div>

            <div class="paginationAjax paginationAttribute">
                <label for="{fieldId form=$form name="paginationAjax"}">{"joppa.content.label.pagination.ajax"|translate}</label>
                <span>{"joppa.content.label.pagination.ajax.description"|translate}</span>
                {field form=$form name="paginationAjax"}
                {fieldErrors form=$form name="paginationAjax"}
            </div>

            <div class="moreShow paginationAttribute">
                <label for="{fieldId form=$form name="moreShow"}">{"joppa.content.label.more.show"|translate}</label>
                <span>{"joppa.content.label.more.show.description"|translate}</span>
                {field form=$form name="moreShow"}
                {fieldErrors form=$form name="moreShow"}
            </div>

            <div class="moreLabel paginationAttribute moreAttribute">
                <label for="{fieldId form=$form name="moreLabel"}">{"joppa.content.label.more.label"|translate}</label>
                <span>{"joppa.content.label.more.label.description"|translate}</span>
                {field form=$form name="moreLabel"}
                {fieldErrors form=$form name="moreLabel"}
            </div>

            <div class="moreNode paginationAttribute moreAttribute">
                <label for="{fieldId form=$form name="moreNode"}">{"joppa.content.label.more.node"|translate}</label>
                <span>{"joppa.content.label.more.node.description"|translate}</span>
                {field form=$form name="moreNode"}
                {fieldErrors form=$form name="moreNode"}
            </div>
        </div>
    </div>

    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>

{/form}