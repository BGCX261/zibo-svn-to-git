{form form=$form}
    <div class="topicsPerPage">
        <label for="{fieldId form=$form name="topicsPerPage"}">{"joppa.forum.label.properties.topics.per.page"|translate}</label>
        <span>{"joppa.forum.label.properties.topics.per.page.description"|translate}</span>
        {field form=$form name="topicsPerPage"}
        {fieldErrors form=$form name="topicsPerPage"}
    </div>

    <div class="postsPerPage">
        <label for="{fieldId form=$form name="postsPerPage"}">{"joppa.forum.label.properties.posts.per.page"|translate}</label>
        <span>{"joppa.forum.label.properties.posts.per.page.description"|translate}</span>
        {field form=$form name="postsPerPage"}
        {fieldErrors form=$form name="postsPerPage"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}