<?php
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//define ROOT_PATH
define('ROOT', __DIR__);
define('ROOT_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/BLOGART26');

//Load env
require_once ROOT . '/includes/libs/DotEnv.php';
(new DotEnv(ROOT.'/.env'))->load();

//defines
require_once ROOT . '/config/defines.php';

//debug
if (getenv('APP_DEBUG') == 'true') {
    require_once ROOT . '/config/debug.php';
}

//load functions
require_once ROOT . '/functions/global.inc.php';

//Connect to database
sql_connect();

//load security
require_once ROOT . '/config/security.php';
