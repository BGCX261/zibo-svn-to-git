<div id="spider">
    <div class="request">
        <h2>{"spider.title"|translate}</h2>
    
        <p class="intro">{"spider.label.intro"|translate}</p>
        
        <p class="url">{"spider.label.url.description"|translate}</p>
        
        {form form=$form}
            <div class="url">
                {field form=$form name="url"}
                {field form=$form name="submit"}
                {field form=$form name="cancel"}
                <span class="loading"></span>

                {fieldErrors form=$form name="url"}
                
                <div class="advancedButton">
                    <a href="#" class="advanced">{"button.advanced"|translate}</a>
                </div>            
            </div>
            
            <div class="advanced">
                <div class="ignore">
                    <label for="{fieldId form=$form name="ignore"}">{"spider.label.ignore"|translate}</label>
                    <span>{"spider.label.ignore.description"|translate}</span>
                    {field form=$form name="ignore"}
                    {fieldErrors form=$form name="ignore"}
                </div>
                
                <div class="delay">
                    <label for="{fieldId form=$form name="delay"}">{"spider.label.delay"|translate}</label>
                    <span>{"spider.label.delay.description"|translate}</span>
                    {field form=$form name="delay"} ms
                    {fieldErrors form=$form name="delay"}
                </div>
            </div>
        {/form}
        
        
        <div class="status">
            <p>{"spider.label.status"|translate}</p>
            <p>{"spider.label.time.elapsed"|translate}</p>
            <p class="current">{"spider.label.current"|translate}</p>
        </div>
    </div>
    
    <div class="dialog"></div>
    
    <div class="report"></div>
    
</div>