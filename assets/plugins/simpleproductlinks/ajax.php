<?php

define('MODX_API_MODE', true);
define('IN_MANAGER_MODE', true);

include_once(__DIR__ . "/../../../index.php");
$modx->db->connect();
if (empty ($modx->config)) {
    $modx->getSettings();
}


if (!isset($_SESSION['mgrValidated'])) {
    die();
}

$modx->invokeEvent('OnManagerPageInit', array(
    'invokedBy' => 'SimpleProductLinks',
//    'tvId' => (int)$_REQUEST['tvid'],
//    'tvName' => $_REQUEST['tvname']
)
);


$mode = (isset($_REQUEST['mode']) && is_scalar($_REQUEST['mode'])) ? $_REQUEST['mode'] : null;
$out = null;


if (isset($modx->pluginCache['SimpleProductLinksProps'])) {
    $pluginParams = $modx->parseProperties($modx->pluginCache['SimpleProductLinksProps'], 'SimpleProductLinks', 'plugin');
} else {
    die();
}

$controllerClass = $pluginParams['controller'];
if (!class_exists('\\SimpleProductLinks\\' . ucfirst($controllerClass . 'Controller'), false)) {
    if (file_exists(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/' . $controllerClass . '.controller.class.php')) {
        require_once(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/' . $controllerClass . '.controller.class.php');
        $controllerClass = '\\SimpleProductLinks\\' . ucfirst($controllerClass . 'Controller');
    } else {
        require_once(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/controller.class.php');
        $controllerClass = '\\SimpleProductLinks\\SimpleProductLinksController';
    }
}

$controller = new $controllerClass($modx,$pluginParams);

if ($controller instanceof \SimpleProductLinks\SimpleProductLinksController) {
    if (!empty($mode) && method_exists($controller, $mode)) {
        $out = call_user_func_array(array($controller, $mode), array());
    } else {
        $out = call_user_func_array(array($controller, 'listing'), array());
    }
    $controller->callExit();
}



echo ($out = is_array($out) ? json_encode($out) : $out);
