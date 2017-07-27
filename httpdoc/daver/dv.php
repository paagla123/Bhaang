<?php
/**
 * DV mock-ups for Kush et al..
 */

error_reporting(E_ALL ^ E_NOTICE);

$page = ($_REQUEST['page']);

if ($page) {
    echo showPage($page);
} else {
    echo showPage('homePage');
}

function showPage($page) {
    $html = file_get_contents($page.'.html');
    return $html;
}
