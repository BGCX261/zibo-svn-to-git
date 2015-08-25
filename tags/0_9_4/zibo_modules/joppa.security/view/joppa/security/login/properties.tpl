{form form=$form}
    <div class="redirect">
        <label for="{fieldId form=$form name="redirect"}">{"joppa.security.label.redirect"|translate}</label>
        {field form=$form name="redirect"}
        {fieldErrors form=$form name="redirect"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}