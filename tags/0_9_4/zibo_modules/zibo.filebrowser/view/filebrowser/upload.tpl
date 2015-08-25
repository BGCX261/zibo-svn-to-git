<h3>{"filebrowser.title.upload"|translate}</h3>

<div id="create">
    {form form=$form}
        <div class="file">
            <label>{"filebrowser.label.file"|translate}</label>
            {field form=$form name="file"}
            {fieldErrors form=$form name="file"}
        </div>
        
        <div class="submit">
            {field form=$form name="submit"}
        </div>
    {/form}
</div>