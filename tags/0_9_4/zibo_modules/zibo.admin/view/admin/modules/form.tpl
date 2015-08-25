<h3>{'modules.title.install'|translate}</h3>
{form form=$form}
    <div class="module">
        {field form=$form name='module'}
        {fieldErrors form=$form name='module'}
    </div>
    
    <div class="submit">
        {field form=$form name='install'}
    </div>
{/form}