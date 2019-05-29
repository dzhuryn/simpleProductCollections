<?php
namespace SimpleProductLinks;
include_once MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/controller.class.php';
include_once MODX_BASE_PATH . 'assets/snippets/DocLister/core/DocLister.abstract.php';
include_once MODX_BASE_PATH . 'assets/snippets/simplegallery/controller/sg_site_content.php';



class ExampleSimpleProductLinksCustomController extends SimpleProductLinksController
{
    public function __construct(\DocumentParser $modx, $params)
    {

        $this->dlParamsForGetAndShowItemsInTab =  array_merge($this->dlParamsForGetAndShowItemsInTab,[
            'prepare'=>'SimpleProductLinks\ExampleSimpleProductLinksCustomController::prepareShow',
            'controller'=>'sg_site_content',
            'tvList'=>'color',
        ]);
        parent::__construct($modx, $params);

    }

    public static function prepareShow(
        array $data = array(),
        \DocumentParser $modx,
        \DocLister $_DL,
        \prepare_DL_Extender $_extDocLister
    ) {

        $image = !empty($data['images'][0]['sg_image'])?$data['images'][0]['sg_image']:'';
        $data['thumb'] = $modx->runSnippet('phpthumb',['input'=>$image,'options'=>'w=50,h=50,far=C']);
        if(substr($data['thumb'],0,1) !='/'){
            $data['thumb'] = '/'.$data['thumb'];
        }
        return $data;
    }

}