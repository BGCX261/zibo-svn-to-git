<h2>{"joppa.title.advanced"|translate}: <span>{$node->name}</span></h2>
<p>{"joppa.label.advanced.description"|translate}</p>

{form form=$form}
    {field form=$form name="id"}
    
    <div class="settings">
        {field form=$form name="settings"}
        <span>{"joppa.label.advanced.field.description"|translate}</span>
        {fieldErrors form=$form name="settings"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}

{if $settings}
<p>
    <a id="nodeSettingsLink" href="#">{"joppa.button.configuration.show"|translate}</a>
</p>

<div id="nodeSettings">
    {$settings}
</div>
{/if}