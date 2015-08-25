{if $notifications}
    {image src="web/images/notify/bell.png" id="notifyIcon" class="active" alt="notify.label.some"|translate title="notify.label.some"|translate}
{else}
    {image src="web/images/notify/bell.disabled.png" id="notifyIcon" alt="notify.label.none"|translate title="notify.label.none"|translate}
{/if}

<div id="notifyContainer">
{foreach from=$notifications item="notification"}
    <div class="notification">{$notification}</div>
{foreachelse}
    <div class="notification">{"notify.label.none"|translate}</div>
{/foreach}    
</div>