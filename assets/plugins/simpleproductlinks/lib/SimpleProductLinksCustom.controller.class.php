<?php namespace SimpleProductLinks;
include 'controller.class.php';
/**
 * Created by PhpStorm.
 * User: Pathologic
 * Date: 27.05.2015
 * Time: 8:08
 */

class SimpleProductLinksCustomController extends SimpleProductLinksController
{

    public function __construct(\DocumentParser $modx,$params)
    {
        parent::__construct($modx,$params);
        $this->dlParams['addWhereList'] = 'c.template = 4';



    }

}
