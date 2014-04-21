<?php

class Zend_View_Helper_CategoryLi
{
    public function CategoryLi($item, $class)
    {
        $view = new Zend_View(array('basePath' => APPLICATION_PATH . '/modules/admin/views'));
        $view->item = $item;
        $view->class = $class;

        return $view->render('block/category_li.phtml');
    }
}