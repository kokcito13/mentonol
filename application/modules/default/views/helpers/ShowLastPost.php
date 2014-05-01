<?php

class Zend_View_Helper_ShowLastPost
{
    public function ShowLastPost($current)
    {
        $view = new Zend_View(array('basePath' => APPLICATION_PATH . '/modules/default/views'));

        $view->current = $current;
        $view->posts = Application_Model_Kernel_Post::getList('post.id', 'DESC', true,
                                                              true, false, true,
                                                              false, false, 4,
                                                              true, 'category_id != 0');


        $view->blocks = Application_Model_Kernel_Block::getList(true)->data;
        foreach ($view->blocks as $key => $value) {
            $view->blocks[$key] = $value->getContent()->getFields();
        }

        return $view->render('block/sidebar_posts.phtml');
    }
}