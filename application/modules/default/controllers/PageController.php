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
    }

    public function sitemapxmlAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $container = new Zend_Navigation();

        $contentPages = Application_Model_Kernel_Page_ContentPage::getList(false, false, false, true, false, 1, false, false, false)->data;
        $categoryList = Application_Model_Kernel_Category::getList(false, false, false, true, false, 1, false, false, false)->data;
        $posts = Application_Model_Kernel_Post::getList(false, false, false, true, false, 1, false, false, false)->data;

        $pages = array_merge($contentPages, $categoryList, $posts);
        foreach ($pages as $page) {
            $container->addPage(Zend_Navigation_Page::factory(array(
                                                                   'uri' => $page->getRoute()->getUrl(),
                                                              )));
        }

        echo $this->view->navigation()->sitemap($container);
    }

    public function sitemapAction()
    {
        $arr = array();
        $this->view->categories = Application_Model_Kernel_Category::getMainCategoryList()->data;
        $i = 0;
        foreach ($this->view->categories as $mainCat) {
            $arr[$i]['item'] = $mainCat;
            $arr[$i]['items'] = $this->getItemsByCategory($mainCat);
            $children = $mainCat->getChildren()->data;
            $j = 0;
            foreach ($children as $cat2) {
                $arr[$i]['childrens'][$j]['item'] = $cat2;
                $arr[$i]['childrens'][$j]['items'] = $this->getItemsByCategory($cat2);
                $children2 = $cat2->getChildren()->data;

                $y = 0;
                foreach ($children2 as $cat3) {
                    $arr[$i]['childrens'][$j]['childrens'][$y]['item'] = $cat3;
                    $arr[$i]['childrens'][$j]['childrens'][$y]['items'] = $this->getItemsByCategory($cat3);
                    $y++;
                }
                $j++;
            }

            $i++;
        }

        $this->view->items = $arr;

        $this->view->title = "Карта сайта";
        $this->view->keywords = "Карта сайта";
        $this->view->description = "Карта сайта";
    }

    function getItemsByCategory($category)
    {
        $whereText = 'post.category_id = '.$category->getId().' AND post.category_id2 IS NULL AND post.category_id3 IS NULL ';
        if ($category->getCurrentLevel() > 1) {
            $whereText = 'post.category_id2 = '.$category->getId().' AND post.category_id3 IS NULL ';
            if ($category->getCurrentLevel() > 2) {
                $whereText = 'post.category_id3 = '.$category->getId();
            }
        }

        $arr = Application_Model_Kernel_Post::getList('post.id', 'DESC', true,
                                                                    true, false, false,
                                                                    true, true, 10,
                                                                    true, $whereText)->data;

        return $arr;
    }
}