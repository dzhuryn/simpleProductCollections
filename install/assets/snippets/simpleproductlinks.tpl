//<?php
/**
 * simpleProductLinks
 *
 * Вывод привязаных товаров
 *
 * @category	snippet
 * @internal	@modx_category Content
 * @internal	@installset base
 * @internal	@overwrite true
 * @internal	@properties 
 */

require_once MODX_BASE_PATH.'assets/plugins/simpleproductlinks/lib/model.php';
$model = new SimpleProductLinks\Model($modx);

$docId = isset($docId)?$docId:$modx->documentIdentifier;
$showCurrent = isset($showCurrent)?$showCurrent:0;

$ids = $model->getRelatedItems($docId); //получаем приязанные к текущему товару
if($showCurrent){
    $ids[] = $docId;
}

$params = array_merge([
    'idType'=>'documents',
    'documents'=>implode(',',$ids),

    'tpl'=>'@CODE:<li><a href="[+url+]">[+title+]</a></li>',
    'ownerTPL'=>'@CODE:<ul>[+dl.wrap+]</ul>'
],$params);

echo $modx->runSnippet('DocLister',$params);

