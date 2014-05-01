<?php

class PostController extends Zend_Controller_Action
{

    public function preDispatch()
    {
        $this->view->blocksArray = array();
        $array = Application_Model_Kernel_Block::getList(true)->data;
        foreach ($array as $key => $value) {
            $this->view->blocksArray[$key] = $value->getContent()->getFields();
        }
    }

    public function showAction()
    {
        $this->view->idPage = (int)$this->_getParam('idPage');
        $this->view->post = Application_Model_Kernel_Post::getByIdPage($this->view->idPage);
        $this->view->contentPage = $this->view->post->getContent()->getFields();

        $title = $this->view->contentPage['title']->getFieldText();
        $keywords = $this->view->contentPage['keywords']->getFieldText();
        $description = $this->view->contentPage['description']->getFieldText();

        $this->view->title = $title;
        $this->view->keywords = $keywords;
        $this->view->description = $description;

        $category = $this->view->post->getCurrentCategory();

        /** @var Application_Model_Kernel_Category $category */
        $this->view->category = $category;
        if ($this->view->category->getCurrentLevel() == 3) {
            $this->view->category = $this->view->category->getParent();
        }

        $categoryList = array( $category );
        if (!is_null($category->getParent())) {
            $categoryList[] = $category->getParent();
            if (!is_null($category->getParent()->getParent())) {
                $categoryList[] = $category->getParent()->getParent();
            }
        }

        $this->view->categoryList = array_reverse($categoryList);
    }
}