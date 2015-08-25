<h2>{"cron.title"|translate}</h2>

{if $table->hasRows()}
{include file="helper/table" table=$table}

{form form=$form}
    <div class="submit">
        <p>{if $isRunning}{"cron.label.started"|translate}{else}{"cron.label.stopped"|translate}{/if}</p>
        {field form=$form name="submit"}
    </div>
{/form}

{if !$isRunning}
<p>{"cron.label.run"|translate}</p>
<code>{$command}</code>
{/if}

{else}
<p>{"cron.label.jobs.none"|translate}</p>
{/if}