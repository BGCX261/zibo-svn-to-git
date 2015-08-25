<h3>{"joppa.comment.title.last"|translate}</h3>
<ul>
{foreach from=$comments item="comment"}
    <li>
        <span class="date">{$comment->dateAdded|formatDate:"shortTime"}</span>
        <a href="{$comment->url}">{$comment->comment|bbcode|strip_tags|truncate:30}</a>
    </li>
{/foreach}
</ul>