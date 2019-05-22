<?php namespace SimpleProductLinks;

/**
 * Created by PhpStorm.
 * User: Pathologic
 * Date: 27.05.2015
 * Time: 8:08
 */

class SimpleProductLinksController
{
    protected $modx = null;
    protected $pluginParams = null;
    public $dlParams = array(
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
    public $dlParamsNoSearch = array();

    /**
     * SelectorController constructor.
     * @param \DocumentParser $modx
     */
    public function __construct(\DocumentParser $modx,$params)
    {
        $this->modx = $modx;
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
            $this->dlParams['search'] = $search;
            $searchContentFields = explode(',', $this->dlParams['searchContentFields']);
            $filters = array();

            if (is_numeric($search)) {
                $filters[] = "content:id:=:{$search}";
            }

            foreach ($searchContentFields as $field) {
                $filters[] = "content:{$field}:{$mode}:{$search}";
            }

            if (!empty($this->dlParams['searchTVFields'])) {
                $searchTVFields = explode(',', $this->dlParams['searchTVFields']);
                foreach ($searchTVFields as $tv) {
                    $filters[] = "tv:{$tv}:{$mode}:{$search}";
                }
            }
            $filters = implode(';', $filters);
            if (!empty($filters)) {
                $filters = "OR({$filters})";
                $this->dlParams['filters'] = $filters;
            }
        }
        $out =  $this->modx->runSnippet("DocLister", $this->dlParams);
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
        $table = $this->modx->getFullTableName('product_links');
        $master = is_scalar($_REQUEST['master']) ? intval($_REQUEST['master']) : '';

        $sql = "select slave from $table where master = $master ";
        $res = $this->modx->db->getColumn('slave',$this->modx->db->query($sql));
        $ids = implode(',',$res);

        $defaultTpl = '@CODE:'.file_get_contents(MODX_BASE_PATH.'assets/plugins/simpleproductlinks/tpl/row.tpl');

        $tpl = empty($this->pluginParams['rowTpl'])?$defaultTpl:$this->pluginParams['rowTpl'];

        $params = [
            'idType'=>'documents',
            'documents'=>$ids,
            'tpl'=>$tpl,


            'showNoPublish'=>'1',

            'tvList'=>'*',
            'tvPrefix'=>'',
        ];


        $out = $this->modx->runSnippet('DocLister',$params);
        return $out;

    }
    public function addLink($master, $slave){
        if ($master && $slave) {
            $table = $this->modx->getFullTableName('product_links');
            $sql = "
                INSERT INTO {$table} ( master, slave)
                VALUES ('$master', '$slave')
                ON DUPLICATE KEY UPDATE  master = '$master', slave = '$slave';
            ";
            $this->modx->db->query($sql);
        }
    }

    public function  remove($master = 0){

        $table = $this->modx->getFullTableName('product_links');

        $master = empty($master) && is_scalar($_REQUEST['master']) ? intval($_REQUEST['master']) : $master;

        $this->modx->db->delete($table,"master = $master or slave = $master");
    }
    public function create(){
        $table = $this->modx->getFullTableName('product_links');

        $master = is_scalar($_REQUEST['master']) ? intval($_REQUEST['master']) : '';
        $slave = is_scalar($_REQUEST['slave']) ? intval($_REQUEST['slave']) : '';
        if($slave == $master) return;

        $this->addLink($master, $slave);
        $this->addLink($slave,$master);

        $sql = "select slave from $table where master in ($master,$slave)";
        $res = $this->modx->db->getColumn('slave',$this->modx->db->query($sql));

        $slaves = array_unique($res);

        foreach ($slaves as $v) {
            foreach ($slaves as $v2) {
                if ($v != $v2) {
                    $sql = "
                    INSERT INTO {$table} ( master, slave)
                    VALUES ('$v', '$v2')
                    ON DUPLICATE KEY UPDATE  master = '$v', slave = '$v2'";

                    $this->modx->db->query($sql);

                }
            }
        }






    }
}
