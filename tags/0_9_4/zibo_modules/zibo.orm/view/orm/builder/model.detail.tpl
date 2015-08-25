<h2>{'orm.title'|translate}</h2>

<h3>{$modelTable->getName()}</h3>

<div id="modelDetail">
    <div class="basicInfo">
        {if $editModelAction}
        <p>{link href=$editModelAction name="orm.button.model.edit.info"}</p>
        {/if}
        <p>{"orm.label.model.log"|translate}: <strong>{if $modelTable->isLogged()}{"label.yes"|translate}{else}{"label.no"|translate}{/if}</strong></p>
        <p>{"orm.label.model.delete.block"|translate}: <strong>{if $modelTable->willBlockDeleteWhenUsed()}{"label.yes"|translate}{else}{"label.no"|translate}{/if}</strong></p>
        <p>{"orm.label.model.class"|translate}: <strong>{$modelClass}</strong></p>
        <p>{"orm.label.data.class"|translate}: <strong>{$dataClass}</strong></p>
    </div>

    <h4>{"orm.label.fields"|translate}</h4>
    {if $addFieldsAction}
    <p>{link href=$addFieldsAction name="orm.button.model.add.fields"}</p>
    {/if}
    {include file="helper/table" table=$fieldTable}
    {if $orderForm}
    {form form=$orderForm}
        {field form=$orderForm name="order"}
        {field form=$orderForm name="submit"}
    {/form}
    {/if}

    <h4>{"orm.label.formats"|translate}</h4>
    {if $editFormatAction}
    <p>{link href=$editFormatAction name="orm.button.model.edit.format"}</p>
    {/if}
    {if $modelTable->getDataFormats()}
    {include file="helper/table" table=$formatTable}
    {else}
    <p>{"orm.label.format.none"|translate}</p>
    {/if}
    
    <h4>{"orm.label.indexes"|translate}</h4>
    {if $editIndexAction}
    <p>{link href=$editIndexAction name="orm.button.model.edit.index"}</p>
    {/if}
    {if $modelTable->getIndexes()}
    {include file="helper/table" table=$indexTable}
    {else}
    <p>{"orm.label.index.none"|translate}</p>
    {/if}
</div>