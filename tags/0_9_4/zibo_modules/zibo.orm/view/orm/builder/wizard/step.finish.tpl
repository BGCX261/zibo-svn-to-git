<p>{"orm.label.finish"|translate}</p>

<h3>{$modelTable->getName()}</h3>

<div class="basicInfo">
    <p>{"orm.label.model.log"|translate}: <strong>{if $modelTable->isLogged()}{"label.yes"|translate}{else}{"label.no"|translate}{/if}</strong></p>
    <p>{"orm.label.model.delete.block"|translate}: <strong>{if $modelTable->willBlockDeleteWhenUsed()}{"label.yes"|translate}{else}{"label.no"|translate}{/if}</strong></p>
{if $modelClass}
    <p>{"orm.label.model.class"|translate}: <strong>{$modelClass}</strong></p>
{/if}
{if $dataClass}
    <p>{"orm.label.data.class"|translate}: <strong>{$dataClass}</strong></p>
{/if}
</div>

<h4>{"orm.label.fields"|translate}</h4>
{include file="helper/table" table=$fieldTable}

<h4>{"orm.label.formats"|translate}</h4>
{if $modelTable->getDataFormats()}
{include file="helper/table" table=$formatTable}
{else}
<p class="indexes">{"orm.label.format.none"|translate}</p>
{/if}

<h4>{"orm.label.indexes"|translate}</h4>
{if $modelTable->getIndexes()}
{include file="helper/table" table=$indexTable}
{else}
<p class="indexes">{"orm.label.index.none"|translate}</p>
{/if}

<div class="modelDefine">
    <label for="{fieldId form=$wizard name="modelDefine"}">{"orm.label.model.define"|translate}</label>
    <span>{"orm.label.model.define.description"|translate}</span>
    {field form=$wizard name="modelDefine"}
    {fieldErrors form=$wizard name="modelDefine"}
</div>