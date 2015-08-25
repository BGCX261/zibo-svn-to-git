<h2>{"filebrowser.title.editor"|translate}</h2>

<div id="editor">
    {form form=$form}
        {field form=$form name="path"}
        
        <div class="path">
            <label>{"filebrowser.label.path"|translate}</label>
            {$path}
        </div>        
        
        <div class="name">
            <label for="{fieldId form=$form name="name"}">{"filebrowser.label.name"|translate}</label>
            {field form=$form name="name"}
            {fieldErrors form=$form name="name"}
        </div>
        
        <div class="content">
            {field form=$form name="content"}
            {fieldErrors form=$form name="content"}
        </div>
        
        <div class="submit">
            {field form=$form name="submit"}
            {field form=$form name="cancel"}
        </div>
    {/form}
</div>