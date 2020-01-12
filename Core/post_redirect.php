<?php
function post_redirect($action, $name, $value)
{
    $html = "<!DOCTYPE html>";
    $html += "<head>";
    $html += "<meta charset='utf-8'>";
    $html += "</head>";
    $html += "<html lang='ja'>";
    $html += "<body onload='document.returnForm.submit();'>";
    $html += "<form method='post' action='" . $action . "'>";
    $html += "<input type='hidden' name='" . $name . "' value='" . $value . "''>";
    $html += "</form>";
    $html += "</body>";
    $html += "</html>";
    return $html;
}
