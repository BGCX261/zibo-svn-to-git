<h2>{"error.report.title"|translate}</h2>

<p>{"error.report.label.description"|translate}</p>

{form form=$form}

    <div class="comment">
        <label for="{fieldId form=$form name="comment"}">{"error.report.label.comment"|translate}</label>
        {field form=$form name="comment"}
        {fieldErrors form=$form name="comment"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
    </div>

    <div class="report">
        <label>{"error.report.label.report"|translate}</label>
        <pre>{$report}</pre>
    </div>

{/form}