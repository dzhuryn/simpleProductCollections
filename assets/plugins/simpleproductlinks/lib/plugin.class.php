<?php namespace SimpleProductLinks;

include_once(MODX_BASE_PATH . 'assets/lib/SimpleTab/plugin.class.php');
use \SimpleTab\Plugin;
/**
 * Class sgPlugin
 * @package SimpleGallery
 */
class splPlugin extends Plugin
{
    public $pluginName = 'SimpleProductLinks';
    public $table = 'sg_images';
    public $tpl = 'assets/plugins/simpleproductlinks/tpl/simpleproductlinks.tpl';
    public $emptyTpl = 'assets/plugins/simpleproductlinks/tpl/empty.tpl';

    public $jsListDefault = 'assets/plugins/simpleproductlinks/js/scripts.json';
    public $jsListCustom = 'assets/plugins/simpleproductlinks/js/custom.json';
    public $cssListDefault = 'assets/plugins/simpleproductlinks/css/styles.json';
    public $cssListCustom = 'assets/plugins/simpleproductlinks/css/custom.json';

    public $pluginEvents = array(

    );

    public function getTplPlaceholders()
    {

        $ph = array(
            'lang'         => $this->lang_attribute,
            'url'          => $this->modx->config['site_url'] . 'assets/plugins/simplegallery/ajax.php',
            'site_url'     => $this->modx->config['site_url'],
            'manager_url'  => MODX_MANAGER_URL,
        );

        return array_merge($this->params, $ph);
    }

    public function checkTable()
    {
        return true;
    }
    public function createTable()
    {
        return true;
    }


}
