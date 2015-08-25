{if $advertisement}
<div class="advertisement">
    {if $advertisement->url}
    <a href="{$advertisement->url}">
        {if $width && $height}
            {image src=$advertisement->image thumbnail="resize" width=$width height=$height}
        {else}
            {image src=$advertisement->image}
        {/if}
    </a>
    {else}
        {if $width && $height}
            {image src=$advertisement->image thumbnail="resize" width=$width height=$height}
        {else}
            {image src=$advertisement->image}
        {/if}
    {/if}
</div>
{/if}