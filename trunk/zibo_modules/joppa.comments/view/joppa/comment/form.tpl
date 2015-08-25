<a name="commentForm"></a>

<h4>{$title|translate}</h4>

{form form=$form}
    {field form=$form name="id"}
    {fieldErrors form=$form name="id"}
    
    {field form=$form name="version"}
    {fieldErrors form=$form name="version"}
    
    {field form=$form name="parent"}
    {fieldErrors form=$form name="parent"}
    
{if $parent}
    <div class="parent">
        {$parent->comment|bbcode}
    </div>
{/if}
    
    <div class="name">
        <label for="{fieldId form=$form name="name"}">{"joppa.comment.label.name"|translate}</label>
        {field form=$form name="name"}
        {fieldErrors form=$form name="name"}
    </div>

    <div class="email">
        <label for="{fieldId form=$form name="email"}">{"joppa.comment.label.email"|translate}</label>
        {field form=$form name="email"}
        {fieldErrors form=$form name="email"}
    </div>

    <div class="comment">
        <label for="{fieldId form=$form name="comment"}">{"joppa.comment.label.comment"|translate}</label>
        {field form=$form name="comment"}
        {fieldErrors form=$form name="comment"}
    </div>

    <div class="submit">
        {field form=$form name="submit"}
    </div>
{/form}