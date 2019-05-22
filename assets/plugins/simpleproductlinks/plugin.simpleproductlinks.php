<?php
if (IN_MANAGER_MODE != 'true') die();
$e = &$modx->event;





if ($e->name == 'OnDocFormRender') {
    include_once(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/plugin.class.php');
    global $modx_lang_attribute, $richtexteditorIds;
    $plugin = new \SimpleProductLinks\splPlugin($modx);
    if ($id) {
        $output = $plugin->render();
    } else {
        $output = $plugin->renderEmpty();
    }
    if ($output) $e->output($output);

}
if ($e->name == 'OnEmptyTrash') {

    if (empty($ids)) return;

    $controllerClass = $e->params['controller'];
    if (!class_exists('\\SimpleProductLinks\\' . ucfirst($controllerClass . 'Controller'), false)) {
        if (file_exists(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/' . $controllerClass . '.controller.class.php')) {
            require_once(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/' . $controllerClass . '.controller.class.php');
            $controllerClass = '\\SimpleProductLinks\\' . ucfirst($controllerClass . 'Controller');
        } else {
            require_once(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/controller.class.php');
            $controllerClass = '\\SimpleProductLinks\\SimpleProductLinksController';
        }
    }
    $controller = new $controllerClass($modx,$e->params);

    foreach ($ids as $id) {
        call_user_func_array(array($controller, 'remove'), array($id));
    }


}
