//<?php
/**
 * SimpleProductLinks
 *
 * SimpleProductLinks
 *
 * @category    plugin
 * @internal    @events OnDocFormRender,OnEmptyTrash
 * @internal    @modx_category Content
 * @internal    @properties {
  "tabName": [
    {
      "label": "Tab name",
      "type": "text",
      "value": "Colors",
      "default": "SimpleGallery",
      "desc": ""
    }
  ],
  "itemTemplates": [
    {
      "label": "Документы каких шаблонов нужно искать",
      "type": "text",
      "value": "5",
      "default": "",
      "desc": ""
    }
  ],
  "controller": [
    {
      "label": "Controller class",
      "type": "text",
      "value": "",
      "default": "",
      "desc": ""
    }
  ],
  "rowTpl": [
    {
      "label": "Шаблон строки товара по правилам DocLister",
      "type": "text",
      "value": "",
      "default": "",
      "desc": ""
    }
  ],
  "templates": [
    {
      "label": "Templates",
      "type": "text",
      "value": "5",
      "default": "",
      "desc": ""
    }
  ],
  "outerTpl": [
    {
      "label": "Шаблон обертки товаров по правилам DocLister",
      "type": "text",
      "value": "",
      "default": "",
      "desc": ""
    }
  ],
  "tvList": [
    {
      "label": "Список тв параметров при выборке документов для вывода списка товаров в вкладке",
      "type": "text",
      "value": "desc",
      "default": "",
      "desc": ""
    }
  ]
}
 * @internal    @disabled 0
 * @internal    @installset base
 */
require MODX_BASE_PATH.'assets/plugins/simpleproductlinks/plugin.simpleproductlinks.php';
