{if $form}
    <h3>{"database.title.default"|translate}</h3>

    {form form=$form}

        <div class="name">
            <label for="{fieldId form=$form name="default"}">{"database.label.default"|translate}</label>
            {field form=$form name="default"}
            {fieldErrors form=$form name="default"}
        </div>
    
        <div class="submit">
            {field form=$form name="save"}
        </div>

    {/form}
{/if}