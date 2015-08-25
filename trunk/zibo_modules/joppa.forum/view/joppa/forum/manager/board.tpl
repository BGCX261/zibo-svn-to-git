<h2>{"joppa.forum.title.manager.structure"|translate}</h2>

{form form=$boardForm}
    {field form=$boardForm name="id"}
    
    <div class="category">
        <label for"{fieldId form=$boardForm name="category"}">{"joppa.forum.label.manager.structure.category"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.board.category.description"|translate}</span>
        {field form=$boardForm name="category"}
        {fieldErrors form=$boardForm name="category"}
    </div>

    <div class="name">
        <label for"{fieldId form=$boardForm name="name"}">{"joppa.forum.label.manager.structure.board.name"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.board.name.description"|translate}</span>
        {field form=$boardForm name="name"}
        {fieldErrors form=$boardForm name="name"}
    </div>

    <div class="description">
        <label for"{fieldId form=$boardForm name="description"}">{"joppa.forum.label.manager.structure.board.description"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.board.description.description"|translate}</span>
        {field form=$boardForm name="description"}
        {fieldErrors form=$boardForm name="description"}
    </div>

    <div class="allowView">
        <label for"{fieldId form=$boardForm name="allowView"}">{"joppa.forum.label.manager.structure.board.allow.view"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.board.allow.view.description"|translate}</span>
        {field form=$boardForm name="allowView"}
        {fieldErrors form=$boardForm name="allowView"}
    </div>
    
    <div class="allowNewTopics">
        <label for"{fieldId form=$boardForm name="allowNewTopics"}">{"joppa.forum.label.manager.structure.board.allow.new.topics"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.board.allow.new.topics.description"|translate}</span>
        {field form=$boardForm name="allowNewTopics"}
        {fieldErrors form=$boardForm name="allowNewTopics"}
    </div>

    <div class="allowNewPosts">
        <label for"{fieldId form=$boardForm name="allowNewPosts"}">{"joppa.forum.label.manager.structure.board.allow.new.posts"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.board.allow.new.posts.description"|translate}</span>
        {field form=$boardForm name="allowNewPosts"}
        {fieldErrors form=$boardForm name="allowNewPosts"}
    </div>

    <div class="moderators">
        <label for"{fieldId form=$boardForm name="moderators"}">{"joppa.forum.label.manager.structure.board.moderators"|translate}</label>
        <span>{"joppa.forum.label.manager.structure.board.moderators.description"|translate}</span>
        {field form=$boardForm name="moderators"}
        {fieldErrors form=$boardForm name="moderators"}
    </div>
    
    <div class="submit">
        {field form=$boardForm name="submit"}
        {field form=$boardForm name="cancel"}
    </div>
{/form}