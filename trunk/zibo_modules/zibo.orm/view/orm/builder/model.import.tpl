<h3>{"orm.title.import"|translate}</h3>

{form form=$importForm}
    <div class="formModelImportFile">
        {field form=$importForm name="file"}
        {fieldErrors form=$importForm name="file"}
    </div>

    <div class="submit">
        {field form=$importForm name="submit"}
    </div>
{/form}