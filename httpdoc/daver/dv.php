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
    $wrapper    = file_get_contents('wrapper.html');
    $body = file_exists($page.'.html') ? file_get_contents($page.'.html') : "<h2>Shoot.  It appears Da Vinci farted. Turn away quickly.</h2>";
    $html       = str_replace("{{content}}",$body,$wrapper);
    return $html;
}
