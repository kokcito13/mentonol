<?php

class Zend_View_Helper_ShowSocial
{
    public function ShowSocial()
    {
        $view = new Zend_View(array('basePath' => APPLICATION_PATH . '/modules/default/views'));

        $view->blocks = Application_Model_Kernel_Block::getList(true)->data;
        foreach ($view->blocks as $key => $value) {
            $view->blocks[$key] = $value->getContent()->getFields();
        }

        return $view->render('block/sidebar_social.phtml');
    }
}