<div class="clipboard">
    <h3>{"filebrowser.title.clipboard"|translate}</h3>
{if $clipboard}
    {include file="helper/table" table=$clipboard}
{else}
    <p>{"filebrowser.label.clipboard.empty"|translate}</p>
{/if}
</div>