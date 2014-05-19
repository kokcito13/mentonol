<?php

class CategoryController extends Zend_Controller_Action
{
    public function showAction()
    {
        $this->view->idPage = (int)$this->_getParam('idPage');
        $this->view->page = Application_Model_Kernel_Category::getByIdPage($this->view->idPage);
        $this->view->contentPage = $this->view->page->getContent()->getFields();

        $lvlText = '';
        if ($this->view->page->getCurrentLevel() > 1) {
            $lvlText = $this->view->page->getCurrentLevel();
        }

        $this->view->posts = Application_Model_Kernel_Post::getList('post.id', 'DESC', true,
                                                                    true, false, false,
                                                                    true, true, Application_Model_Kernel_Post::ITEM_ON_MAIN_PAGE,
                                                                    true, 'post.category_id'.$lvlText.' = '.$this->view->page->getId());

        $title       = trim($this->view->contentPage['title']->getFieldText());
        $keywords    = trim($this->view->contentPage['keywords']->getFieldText());
        $description = trim($this->view->contentPage['description']->getFieldText());

        $this->view->title       = $title;
        $this->view->keywords    = $keywords;
        $this->view->description = $description;
    }
}