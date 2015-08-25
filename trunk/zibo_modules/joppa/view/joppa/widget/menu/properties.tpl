{form form=$form}
    <div class="parent">
        <label for="{fieldId form=$form name="parent"}">{"joppa.widget.menu.label.parent"|translate}</label>
        <span>{"joppa.widget.menu.label.parent.description"|translate}</span>
        {field form=$form name="parent"}
        {fieldErrors form=$form name="parent"}
    </div>

    <div class="depth">
        <label for="{fieldId form=$form name="depth"}">{"joppa.widget.menu.label.depth"|translate}</label>
        <span>{"joppa.widget.menu.label.depth.description"|translate}</span>
        {field form=$form name="depth"}
        {fieldErrors form=$form name="depth"}
    </div>

    <div class="showTitle">
        <label for="{fieldId form=$form name="showTitle"}">{"joppa.widget.menu.label.title.show"|translate}</label>
        <span>{"joppa.widget.menu.label.title.show.description"|translate}</span>
        {field form=$form name="showTitle"}
        {fieldErrors form=$form name="showTitle"}
    </div>
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}