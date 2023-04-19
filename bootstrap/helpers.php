<?php

function title($text = '')
{
    return ucwords(str_replace(['_', '-'], ' ', $text));
}

function reverse_title($text = '')
{
    return strtolower(str_replace(' ', '_', $text));
}
