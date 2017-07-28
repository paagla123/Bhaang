<?php
/**
 * DV mock-ups for Kush et al..
 */

error_reporting(E_ALL ^ E_NOTICE);

$page   = ($_REQUEST['page']);
$action = ($_REQUEST['action']);

if ($page) {
    if ($action) {
        doAction($_POST);
    }
    echo showPage($page);
    } else {
    echo showPage('homePage');
}

function showPage($page) {
    $vars       = file_get_contents("credentials.json"); // Get this user's information.  Later this is in DB
    $vars       = json_decode($vars, true);
    
    // Create array $vars_array that has the {{var}} = value stuff we need to inject within our UI tempaltes
    foreach ($vars as $k => $v) {
        $vars_array['{{'.$k.'}}'] = $v;
        unset($vars_array[$k]);
    }

    // Get our wrapper html, insert the body into it, and then repace all our vars using vars_array
    $wrapper    = file_get_contents('wrapper.html');
    $body       = file_exists($page.'.html') ? file_get_contents($page.'.html') : "<h2>Shoot.  It appears Da Vinci farted. Turn away quickly.</h2>";
    $html       = str_replace("{{content}}",$body,$wrapper);
    $html       = str_replace(array_keys($vars_array), $vars_array, $html);
    return $html;
}

function doAction($POST) {
    if ($POST['action'] == "saveProfileSettings") {
        file_put_contents('credentials.json', json_encode($POST));
    }
    
    if ($POST['action'] == "sendTest") {
        exec("lynx -accept_all_cookies -dump http://daverosend.mindfirestudio.net/dv_trigger/send_test.html > /dev/null 2>&1 &");
    }
}