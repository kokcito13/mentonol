<?php

class CategoryController extends Zend_Controller_Action
{
    public function showAction()
    {
        $this->view->idPage = (int)$this->_getParam('idPage');
        $this->view->page = Application_Model_Kernel_Category::getByIdPage($this->view->idPage);
        $this->view->contentPage = $this->view->page->getContent()->getFields();


        $title = $this->view->contentPage['title']->getFieldText();
        $keywords = $this->view->contentPage['keywords']->getFieldText();
        $description = $this->view->contentPage['description']->getFieldText();

        $this->view->title = $title;
        $this->view->keywords = $keywords;
        $this->view->description = $description;
        $this->view->text = $this->view->contentPage['content']->getFieldText();
    }
}