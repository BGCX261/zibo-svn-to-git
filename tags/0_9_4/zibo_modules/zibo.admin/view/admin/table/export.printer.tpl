<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {head}
    </head>
    <body class="export">
{if $title}
        <h1>{$title}</h1>
{/if}
        <table>
{foreach from=$headers item="header"}
            {$header->getHtml()}
{/foreach}
{foreach from=$rows item="row"}
            {$row->getHtml()}
{/foreach}        
        </table>        
    </body>
</html>