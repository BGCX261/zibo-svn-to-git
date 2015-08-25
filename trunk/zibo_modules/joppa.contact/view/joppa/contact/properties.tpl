{form form=$form}
    <div class="recipient">
        <label for="{fieldId form=$form name="recipient"}">{"joppa.widget.contact.label.recipient"|translate}</label>
        {field form=$form name="recipient"}
        {fieldErrors form=$form name="recipient"}
    </div>

    <div class="subject">
        <label for="{fieldId form=$form name="subject"}">{"joppa.widget.contact.label.subject"|translate}</label>
        {field form=$form name="subject"}
        {fieldErrors form=$form name="subject"}
    </div>
    
    <div class="submit">
        {field form=$form name="save"}
        {field form=$form name="cancel"}
    </div>
{/form}