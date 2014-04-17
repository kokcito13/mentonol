<?php

class IndexController extends Zend_Controller_Action
{

    public function preDispatch()
    {
        $this->view->menu = 'main';
        $this->view->blocksArray = array();
        $array = Application_Model_Kernel_Block::getList(true)->data;
        foreach ($array as $key => $value) {
            $this->view->blocksArray[$key] = $value->getContent()->getFields();
        }
    }

    public function indexAction()
    {
        $this->view->idPage      = (int)$this->_getParam('idPage');
        $this->view->page = Application_Model_Kernel_Page_ContentPage::getByPageId($this->view->idPage);
        $this->view->contentPage = $this->view->page->getContent()->getFields();

        $title       = trim($this->view->contentPage['title']->getFieldText());
        $keywords    = trim($this->view->contentPage['keywords']->getFieldText());
        $description = trim($this->view->contentPage['description']->getFieldText());

        $this->view->text        = $this->view->contentPage['content']->getFieldText();
        $this->view->title       = $title;
        $this->view->keywords    = $keywords;
        $this->view->description = $description;
    }
}