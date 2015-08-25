<h2>{translate key="queue.title.job" job=$data->id}</h2>

<table class="queueDetail">
    <tr>
        <td class="key">{"queue.label.class"|translate}</td>
        <td class="value">{$data->getJobClassName()}</td>
    </tr>
    <tr>
        <td class="key">{"queue.label.queue"|translate}</td>
        <td class="value">{$data->queue}</td>
    </tr>
    <tr>
        <td class="key">{"queue.label.added"|translate}</td>
        <td class="value">{$data->dateAdded|formatDate:"j F Y H:i:s"}</td>
    </tr>
    {if $data->dateScheduled}
    <tr>
        <td class="key">{"queue.label.scheduled"|translate}</td>
        <td class="value">{$data->dateScheduled|formatDate:"j F Y H:i:s"}</td>
    </tr>
    {/if}
    <tr>
        <td class="key">{"queue.label.status"|translate}</td>
        <td class="value">
            <div id="jobStatus" class="{$data->getStatusClass()}"></div>
            {if $data->isError}
            {"queue.label.status.error"|translate}
            {elseif $data->isInProgress}
            {"queue.label.status.progress"|translate}
            {else}
            {"queue.label.status.waiting"|translate}
            {/if}
        </td>
    </tr>
    {if $data->isError}
    <tr>
        <td class="key">{"queue.label.error"|translate}</td>
        <td class="value"><pre>{$data->error}</pre></td>
    </tr>
    {/if}
</table>

<p><a href="{$backUrl}">{"button.back"|translate}</a></p>