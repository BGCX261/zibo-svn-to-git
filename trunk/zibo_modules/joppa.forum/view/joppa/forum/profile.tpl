<h2>{$profile->name}</h2>

<div id="forumProfile">
    {if $profile->location}
    <div class="location">
        {$profile->location}
    </div>
    {/if}

    {if $profile->gender}
    <div class="gender">
        {$profile->gender}
    </div>
    {/if}

    {if $profile->birthday}
    <div class="birthday">
        {$profile->birthday}
    </div>
    {/if}

    {if $profile->website}
    <div class="website">
        <a href="{$profile->website}" target="_blank">{$profile->website}</a>
    </div>
    {/if}

    {if $profile->msn}
    <div class="msn">
        {$profile->msn}
    </div>
    {/if}

    {if $profile->myspace}
    <div class="myspace">
        <a href="{$profile->myspace}" target="_blank">{$profile->myspace}</a>
    </div>
    {/if}

    {if $profile->facebook}
    <div class="facebook">
        <a href="{$profile->facebook}" target="_blank">{$profile->facebook}</a>
    </div>
    {/if}

    {if $profile->signature}
    <div class="signature">
        {$profile->signature}
    </div>
    {/if}

    <div class="numPosts">
        Posts: {$profile->numPosts}
    </div>
    
</div>