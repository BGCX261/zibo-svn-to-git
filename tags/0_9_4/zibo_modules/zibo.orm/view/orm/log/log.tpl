<div id="modelLogDetail">

<h2>{"orm.title.log"|translate}</h2>

<table class="details">
    <tr>
        <td class="field">{"orm.label.date"|translate}</td>
        <td class="value">{$log->dateAdded|formatDate:"j M Y H:i:s"}</td>
    </tr>
    <tr>
        <td class="field">{"orm.label.user"|translate}</td>
        <td class="value">{$log->user}</td>
    </tr>
    <tr>
        <td class="field">{"orm.label.action"|translate}</td>
        <td class="value">{$log->action}</td>
    </tr>
    <tr>
        <td class="field">{"orm.label.data"|translate}</td>
        <td class="value">{$log->dataModel} #{$log->dataId}</td>
    </tr>
</table>

<h3>{"orm.title.log.changes"|translate}</h3>

{include file="helper/table" table=$table}

<p><a href="{$urlBack}">{"orm.button.log.back"|translate}</a></p>

</div>