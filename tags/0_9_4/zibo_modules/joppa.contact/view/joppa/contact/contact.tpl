{form form=$form}
    <div class="name">
        <label for="{fieldId form=$form name="name"}">{"joppa.contact.label.name"|translate}</label>
        {field form=$form name="name"}
        {fieldErrors form=$form name="name"}
    </div>

    <div class="email">
        <label for="{fieldId form=$form name="email"}">{"joppa.contact.label.email"|translate}</label>
        {field form=$form name="email"}
        {fieldErrors form=$form name="email"}
    </div>

    <div class="message">
        <label for="{fieldId form=$form name="message"}">{"joppa.contact.label.message"|translate}</label>
        {field form=$form name="message"}
        {fieldErrors form=$form name="message"}
    </div>
    
    <div class="captcha">
        {subview name="captcha"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
    </div>
{/form}