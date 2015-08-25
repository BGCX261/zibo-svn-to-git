<div class="exception">
    <div class="message">{$exception.message}</div>
{if $exception.trace}
    <div class="trace">
        {$exception.trace|escape:'html'|nl2br} 
    </div>
{/if}
{if $exception.cause}
    <div class="cause">
        Caused by:
        {include file="admin/exception/exception" exception=$exception.cause}
    </div>
{/if}
</div>
