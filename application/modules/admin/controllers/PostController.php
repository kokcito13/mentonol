<?php

class Admin_PostController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        if (!Application_Model_Admin_Admin::isAuthorized())
            $this->_redirect($this->view->url(array(), 'admin-login'));
        else
            $this->view->blocks = (object)array('menu' => true);
        $this->view->add = false;
        $this->view->back = false;
        $this->view->breadcrumbs = new Application_Model_Kernel_Breadcrumbs();
        $this->view->page = !is_null($this->_getParam('page')) ? $this->_getParam('page') : 1;
        $this->view->cat = !is_null($this->_getParam('cat')) ? $this->_getParam('cat') : 0;

        $this->view->categoryList = Application_Model_Kernel_Category::getList(false, false, true, false, false, false, false, false, false, true, false);
    }

    public function indexAction()
    {
        $this->view->add = (object)array(
            'link' => $this->view->url(array('page'=>$this->view->page,'cat'=>$this->view->cat), 'admin-post-add'),
            'alt'  => 'Добавить',
            'text' => 'Добавить'
        );

        $this->view->page = (int)$this->_getParam('page');

        $this->view->breadcrumbs->add('Список пуликаций', '');
        $this->view->headTitle()->append('Список пуликаций');
        $this->view->postList = Application_Model_Kernel_Post::getList(false, false, true,
            true, false, false,
            $this->view->page, 15, false,
            true, 'category_id = '. $this->view->cat);
    }

    public function addAction()
    {
        $this->view->langs = Kernel_Language::getAll();
        $this->view->idPage = null;
        $this->view->tinymce = true;
        $this->view->back = true;
        $this->view->edit = false;

        $this->view->idPhoto1 = 0;
        $this->view->post = new Application_Model_Kernel_Post(null,
            null, $this->view->cat, null, null,
            null, null, null,
            time(), Application_Model_Kernel_Page_ContentPage::STATUS_SHOW, 0);

        if ($this->getRequest()->isPost()) {
            $data = (object)$this->getRequest()->getPost();
            try {
                $this->view->idPhoto1 = (int)$data->idPhoto1;
                $this->view->photo1 = Application_Model_Kernel_Photo::getById($this->view->idPhoto1);

                $url = new Application_Model_Kernel_Routing_Url($data->url_category."/".$data->url);
                $defaultParams = new Application_Model_Kernel_Routing_DefaultParams();
                $route = new Application_Model_Kernel_Routing(null, Application_Model_Kernel_Routing::TYPE_ROUTE, '~public', 'default', 'post', 'show', $url, $defaultParams, Application_Model_Kernel_Routing::STATUS_ACTIVE);

                $contentManager = Application_Model_Kernel_Content_Fields::setCMwithFields($data->content);

                $this->view->post->setContentManager($contentManager);
                $this->view->post->setRoute($route);

                $this->view->post->setMainPhotoId($this->view->idPhoto1);
                $this->view->post->setCategory(Application_Model_Kernel_Category::getById($data->category));
                $this->view->post->validate($data);
                $this->view->post->save();

                $this->_redirect($this->view->url(array('page' => 1, 'cat'=>$this->view->cat), 'admin-post-index'));
            } catch (Application_Model_Kernel_Exception $e) {
                $this->view->ShowMessage($e->getMessages());
            } catch (Exception $e) {
                $this->view->ShowMessage($e->getMessage());
            }
        }

        $this->view->breadcrumbs->add('Добавить пуликацию', '');
        $this->view->headTitle()->append('Добавить');
    }

    public function editAction()
    {
        $this->view->langs = Kernel_Language::getAll();
        $this->view->back = true;
        $this->_helper->viewRenderer->setScriptAction('add');
        $this->view->tinymce = true;
        $this->view->edit = true;
        $this->view->post = Application_Model_Kernel_Post::getById((int)$this->_getParam('id'));

        $this->view->idPhoto1 = $this->view->post->getMainPhotoId();

        $getContent = $this->view->post->getContentManager()->getContent();
        $this->view->idPage = $this->view->post->getIdPage();
        if ($this->getRequest()->isPost()) {
            $data = (object)$this->getRequest()->getPost();
            try {
                Application_Model_Kernel_Content_Fields::setFieldsForModel($data->content, $getContent, $this->view->post);

                $this->view->idPhoto1 = (int)$data->idPhoto1;
                $this->view->photo1 = Application_Model_Kernel_Photo::getById($this->view->idPhoto1);
                $this->view->post->setMainPhotoId($this->view->idPhoto1);
                
                $category = Application_Model_Kernel_Category::getById($data->category);
                $this->view->post->setCategory($category);

                $this->view->post->getRoute()->setUrl($data->url_category."/".$data->url);
                $this->view->post->validate($data);
                $this->view->post->save();

                $this->_redirect($this->view->url(array('page' => 1, 'cat'=>$this->view->cat), 'admin-post-index'));
            } catch (Application_Model_Kernel_Exception $e) {
                $this->view->ShowMessage($e->getMessages());
            } catch (Exception $e) {
                $this->view->ShowMessage($e->getMessage());
            }
        } else {
            $this->view->photo1 = Application_Model_Kernel_Photo::getById($this->view->idPhoto1);

            $url = Application_Model_Kernel_Routing::getById($this->view->post->getIdRoute())->getUrl();
            $urlPart = explode('/', $url);
            $_POST['url'] = $urlPart[count($urlPart)-1];
            unset($urlPart[count($urlPart)-1]);
            $_POST['url_category'] = join('/', $urlPart);

            $_POST['content'] = $this->view->post->getContentManager()->getContents();
            foreach ($this->view->langs as $lang) {
                if (isset($_POST['content'][$lang->getId()]))
                    foreach ($_POST['content'][$lang->getId()] as $value)
                        $_POST['content'][$lang->getId()][$value->getFieldName()] = $value->getFieldText();
            }
        }
        $this->view->breadcrumbs->add('Редактировать', '');
        $this->view->headTitle()->append('Редактировать');
    }

    public function statuschangeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->isPost()) {
            $data = (object)$this->getRequest()->getPost();

            $this->view->project = Application_Model_Kernel_Product::getById((int)$data->idProduct);
            if ($this->view->project->getProductStatusPopular() != 2)
                $this->view->project->setProductStatusPopular(2);
            else
                $this->view->project->setProductStatusPopular(1);
            $this->view->project->save();
            echo 1;
            exit();
        }
        echo 0;
        exit();
    }
}