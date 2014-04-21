<?php
class PageController extends Zend_Controller_Action
{

    public function preDispatch()
    {
    }

    public function showAction()
    {
        $this->view->idPage = (int)$this->_getParam('idPage');
        $this->view->page = Application_Model_Kernel_Page_ContentPage::getByPageId($this->view->idPage);
        $this->view->contentPage = $this->view->page->getContent()->getFields();

        $this->view->title = $this->view->contentPage['title']->getFieldText();
        $this->view->keywords = $this->view->contentPage['keywords']->getFieldText();
        $this->view->description = $this->view->contentPage['description']->getFieldText();

        $this->view->headText = isset($this->view->contentPage['head'])?$this->view->contentPage['head']->getFieldText():'';

        $this->view->menu = $this->view->page->getRoute()->getUrl();
    }

    public function sitemapxmlAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $container = new Zend_Navigation();

        $contentPages = Application_Model_Kernel_Page_ContentPage::getList(false, false, false, true, false, 1, false, false, false)->data;
        $categoryList = Application_Model_Kernel_Category::getList(false, false, false, true, false, 1, false, false, false)->data;

        $pages = array_merge($contentPages, $categoryList);
        foreach ($pages as $page) {
            $container->addPage(Zend_Navigation_Page::factory(array(
                                                                   'uri' => $page->getRoute()->getUrl(),
                                                              )));
        }

        echo $this->view->navigation()->sitemap($container);
    }
}