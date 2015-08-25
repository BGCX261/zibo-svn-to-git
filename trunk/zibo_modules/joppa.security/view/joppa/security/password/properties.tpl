{form form=$form}
    <div class="subject">
        <label for="{fieldId form=$form name="subject"}">{"joppa.security.label.subject"|translate}</label>
        <span>{"joppa.security.label.subject.description.password"|translate}</span>
        {field form=$form name="subject"}
        {fieldErrors form=$form name="subject"}
    </div>

    <div class="message">
        <label for="{fieldId form=$form name="message"}">{"joppa.security.label.message"|translate}</label>
        <span>{"joppa.security.label.message.description.password"|translate}</span>
        <span>{"joppa.security.label.message.variables"|translate}</span>
        {field form=$form name="message"}
        {fieldErrors form=$form name="message"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}