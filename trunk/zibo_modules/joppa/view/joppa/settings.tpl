<h2>{"joppa.title.settings"|translate}</h2>

{form form=$form}
    <div class="isPublished">
        <label for="{fieldId form=$form name="isPublished"}">{"joppa.label.publish.default"|translate}</label>
        <span>{"joppa.label.publish.default.description"|translate}</span>
        {field form=$form name="isPublished"}
    </div>

    <div class="submit">
        {field form=$form name="submit"}
    </div>
{/form}