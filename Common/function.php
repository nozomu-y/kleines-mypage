<?php
function getGitBranch()
{
    $shellOutput = [];
    exec('git branch | ' . "grep ' * '", $shellOutput);
    foreach ($shellOutput as $line) {
        if (strpos($line, '* ') !== false) {
            return trim(str_replace('* ', '', $line));
        }
    }
    return null;
}

function format_price($price)
{
    return '￥' . number_format($price);
}
