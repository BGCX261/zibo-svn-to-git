<h2>{"filebrowser.title.rename"|translate}</h2>

<div id="rename">
    {form form=$form}
        {field form=$form name="path"}
        
        <div class="path">
            <label>{"filebrowser.label.path"|translate}</label>
            {$path}
        </div>
        
        <div class="name">
            <label for="{fieldId form=$form name="name"}">{"filebrowser.label.name.new"|translate}</label>
            {field form=$form name="name"}
            {fieldErrors form=$form name="name"}
        </div>
        
        <div class="submit">
            {field form=$form name="submit"}
            {field form=$form name="cancel"}
        </div>
    {/form}
</div>