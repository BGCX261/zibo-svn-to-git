{form form=$form}
    <div class="posts">
        <label for="{fieldId form=$form name="posts"}">{"joppa.forum.label.properties.posts.preview"|translate}</label>
        <span>{"joppa.forum.label.properties.posts.preview.description"|translate}</span>
        {field form=$form name="posts"}
        {fieldErrors form=$form name="posts"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}