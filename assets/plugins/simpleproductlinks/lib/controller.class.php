<?php namespace SimpleProductLinks;

/**
 * Created by PhpStorm.
 * User: Pathologic
 * Date: 27.05.2015
 * Time: 8:08
 */

class SimpleProductLinksController
{
    protected $model;
    protected $modx = null;
    protected $pluginParams = null;

    public $dlParamsForSearchItems = array(
        'api'                 => 'id,pagetitle,parent,html,text',
        'JSONformat'          => 'old',
        'display'             => 10,
        'offset'              => 0,
        'sortBy'              => 'c.id',
        'sortDir'             => 'desc',
        'parents'             => 0,
        'showParent'          => 1,
        'depth'               => 10,
        'searchContentFields' => 'c.id,c.pagetitle,c.longtitle',
        'searchTVFields'      => '',
        'idField'             => 'id',
        'textField'           => 'pagetitle',
        'showNoPublish'        => '1',
        'prepare'             => 'SimpleProductLinks\SimpleProductLinksController::prepare'
    );


    public $dlParamsForGetAndShowItemsInTab = [
        'idType'=>'documents',
        'showNoPublish'=>'1',
        'tvPrefix'=>'',

    ];

    /**
     * SelectorController constructor.
     * @param \DocumentParser $modx
     */
    public function __construct(\DocumentParser $modx,$params)
    {
        include_once(MODX_BASE_PATH . 'assets/plugins/simpleproductlinks/lib/model.php');
        $this->modx = $modx;
        $this->model = new Model($modx);
        $this->pluginParams = $params;

    }

    /**
     *
     */
    public function callExit()
    {
        if ($this->isExit) {
            echo $this->output;
            exit;
        }
    }

    /**
     * @param array $data
     * @param \DocumentParser $modx
     * @param \DocLister $_DL
     * @param \prepare_DL_Extender $_extDocLister
     * @return array
     */
    public static function prepare(
        array $data = array(),
        \DocumentParser $modx,
        \DocLister $_DL,
        \prepare_DL_Extender $_extDocLister
    ) {
        if (($docCrumbs = $_extDocLister->getStore('currentParents' . $data['parent'])) === null) {
            $modx->documentObject['id'] = $data['id'];
            $docCrumbs = rtrim($modx->runSnippet('DLcrumbs', array(
                'ownerTPL'   => '@CODE:[+crumbs.wrap+]',
                'tpl'        => '@CODE: [+title+] /',
                'tplCurrent' => '@CODE: [+title+] /',
                'hideMain'   => '1'
            )), ' /');
            $_extDocLister->setStore('currentParents' . $data['parent'], $docCrumbs);
        }
        if ($search = $_DL->getCFGDef('search')) {
            $html = preg_replace("/(" . preg_quote($search, "/") . ")/iu", "<b>$0</b>",
                $data[$_DL->getCFGDef('textField', 'pagetitle')]);
        } else {
            $html = $data[$_DL->getCFGDef('textField', 'pagetitle')];
        }
        $data['text'] = "{$data[$_DL->getCFGDef('idField','id')]}. {$data[$_DL->getCFGDef('textField','pagetitle')]}";
        $data['html'] = "<div><small>{$docCrumbs}</small><br>{$data['id']}. {$html}</div>";

        return $data;
    }

    /**
     * @return string
     */
    public function listing()
    {
        $search = is_scalar($_REQUEST['q']) ? $_REQUEST['q'] : '';
        if (!empty($search)) {
            if (substr($search, 0, 1) == '=') {
                $search = substr($search, 1);
                $mode = '=';
            } else {
                $mode = 'like';
            }


            $this->dlParamsForSearchItems['search'] = $search;
            $searchContentFields = explode(',', $this->dlParamsForSearchItems['searchContentFields']);
            $filters = array();

            if (is_numeric($search)) {
                $filters[] = "content:id:=:{$search}";
            }

            foreach ($searchContentFields as $field) {
                $filters[] = "content:{$field}:{$mode}:{$search}";
            }

            if (!empty($this->dlParamsForSearchItems['searchTVFields'])) {
                $searchTVFields = explode(',', $this->dlParamsForSearchItems['searchTVFields']);
                foreach ($searchTVFields as $tv) {
                    $filters[] = "tv:{$tv}:{$mode}:{$search}";
                }
            }
            $filters = implode(';', $filters);
            if (!empty($filters)) {
                $filters = "OR({$filters})";
                $this->dlParamsForSearchItems['filters'] = $filters;
            }
        }


        if(!empty($this->pluginParams['itemTemplates']) && empty($this->dlParamsForSearchItems['addWhereList'])){
            $this->dlParamsForSearchItems['addWhereList'] = 'template in ('.$this->pluginParams['itemTemplates'].')';
        }
        $out =  $this->modx->runSnippet("DocLister", $this->dlParamsForSearchItems);
        $output = [];
        if (!is_array($out)) {
            $out = json_decode($out,true);
        }

        foreach ($out as $el) {
            $output[] = $el;
        }
        return json_encode(['results'=>$output]);

    }
    public function getList(){
        $master = is_scalar($_REQUEST['master']) ? intval($_REQUEST['master']) : '';
        $res = $this->model->getRelatedItems($master);
        $ids = implode(',',$res);

        $defaultRowTpl = '@FILE:simpleProductCollections/row';
        $defaultOuterTpl = '@FILE:simpleProductCollections/outer';

        $tpl = empty($this->pluginParams['rowTpl'])?$defaultRowTpl:$this->pluginParams['rowTpl'];
        $ownerTPL = empty($this->pluginParams['outerTpl'])?$defaultOuterTpl:$this->pluginParams['outerTpl'];

        $params = [
            'documents'=>$ids,
            'tpl'=>$tpl,
            'ownerTPL'=>$ownerTPL,
            'tvList'=>$this->pluginParams['tvList'],
        ];


        $params = array_merge($params,$this->dlParamsForGetAndShowItemsInTab);



        $out = $this->modx->runSnippet('DocLister',$params);
        return $out;

    }


    /**
     * Метод создает связть товаров
     * В первую очередь связует текущий и дочерний, а также с их дочерними
     */
    public function create(){

        $master = is_scalar($_REQUEST['master']) ? intval($_REQUEST['master']) : '';
        $slave = is_scalar($_REQUEST['slave']) ? intval($_REQUEST['slave']) : '';
        if($slave == $master) return;


        $this->model->addRelation($master,$slave);
        $this->model->addRelation($slave,$master);

        $res = $this->model->getFullRelations($master,$slave);
        $slaves = array_unique($res);

        foreach ($slaves as $v) {
            foreach ($slaves as $v2) {
                if ($v != $v2) {
                    $this->model->addRelation($v,$v2);
                }
            }
        }
    }

    /*
     * Удаляет связи товара из дочерними
     */
    public function  remove($master = 0){
        $master = empty($master) && is_scalar($_REQUEST['master']) ? intval($_REQUEST['master']) : $master;
        $this->model->removeRelation($master);
    }
}
