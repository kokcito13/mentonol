<?php

class Application_Model_Kernel_Post extends Application_Model_Kernel_Page
{

    private $id;
    private $mainPhotoId;
    private $mainPhoto = null;

    private $categoryId = null;
    private $categoryId2 = null;
    private $categoryId3 = null;

    private $currentCategory = null;

    const _tableName = 'post';

    const ITEM_ON_PAGE = 3;
    const ITEM_ON_MAIN_PAGE = 8;

    public function __construct(
        $id,
        $mainPhotoId, $category_id, $category_id2, $category_id3,
        $idPage, $idRoute, $idContentPack,
        $pageEditDate, $pageStatus, $position
    )
    {
        parent::__construct($idPage, $idRoute, $idContentPack, $pageEditDate, $pageStatus, self::TYPE_POST, $position);
        $this->id = $id;

        $this->mainPhotoId = $mainPhotoId;
        $this->categoryId = $category_id;
        $this->categoryId2 = $category_id2;
        $this->categoryId3 = $category_id3;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function save()
    {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();
        try {
            $db->beginTransaction();
            $insert = is_null($this->_idPage);
            $this->savePageData();
            $data = array(
                'idPage'   => $this->getIdPage(),
                'main_photo_id' => $this->mainPhotoId,
                'category_id' => $this->categoryId,
                'category_id2' => $this->categoryId2,
                'category_id3' => $this->categoryId3
            );
            if ($insert) {
                $db->insert(self::_tableName, $data);
                $this->id = $db->lastInsertId();
            }
            else {
                $db->update(self::_tableName, $data, 'id = ' . intval($this->id));
            }
            $db->commit();
//            $this->clearCache();
        } catch (Exception $e) {
            $db->rollBack();
            Application_Model_Kernel_ErrorLog::addLogRow(Application_Model_Kernel_ErrorLog::ID_SAVE_ERROR, $e->getMessage(), ';product.php');
            throw new Exception($e->getMessage());
        }
    }

    private function clearCache()
    {
        if (!is_null($this->getidProject())) {
            $cachemanager = Zend_Registry::get('cachemanager');
            $cache = $cachemanager->getCache('product');
            if (!is_null($cache)) {
                $cache->remove($this->getid());
            }
        }
    }

    public function validate($data = false)
    {
        $e = new Application_Model_Kernel_Exception();
        $this->getRoute()->validate($e);
        $this->validatePageData($e);

        if ($data != false) {
            $data->url = trim($this->getRoute()->getUrl());
            if (empty($data->url))
                throw new Exception(' Пустой URL ');
            $langs = Kernel_Language::getAll();
            foreach ($langs as $lang) {
                if (empty($data->content[$lang->getId()]['name']))
                    throw new Exception(' Пустое поле "Название" ' . $lang->getFullName());
            }
        }

        if ((bool)$e->current())
            throw $e;
    }

    private static function getSelf(stdClass &$data)
    {
        return new self($data->id,
                        $data->main_photo_id, $data->category_id, $data->category_id2, $data->category_id3,
                        $data->idPage, $data->idRoute, $data->idContentPack,
                        $data->pageEditDate, $data->pageStatus, $data->position
                        );
    }

    public static function loadCache($id)
    {
        $cachemanager = Zend_Registry::get('cachemanager');
        $cache = $cachemanager->getCache('project');

        return $cache->load($id);
    }

    public static function getById($id)
    {

//		$cachemanager = Zend_Registry::get('cachemanager');
//		$cache = $cachemanager->getCache('department');
//		if (($project = $cache->load($idProject)) !== false) {
//			return $project;
//		} else {
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(self::_tableName);
        $select->join('pages', self::_tableName.'.idPage = pages.idPage');
        $select->where('id = ?', $id);
        $select->limit(1);
        if (($productData = $db->fetchRow($select)) !== false) {
//				$project->completelyCache();
            return self::getSelf($productData);
        } else {
            throw new Exception(self::ERROR_INVALID_ID);
        }
//		}
    }

    public static function getByIdPage($idPage)
    {
        $idPage = intval($idPage);

        $db = Zend_Registry::get('db');
        $select = $db->select()->from(self::_tableName);
        $select->join('pages', self::_tableName.'.idPage = pages.idPage');
        $select->where('pages.idPage = ?', $idPage);
        $select->limit(1);
        if (($productData = $db->fetchRow($select)) !== false) {
            return self::getSelf($productData);
        }
        else {
            throw new Exception(self::ERROR_INVALID_ID.' - PAGE');
        }
    }

    public function completelyCache()
    {
        $cachemanager = Zend_Registry::get('cachemanager');
        $cache = $cachemanager->getCache('product');
        $cache->load($this->getIdPage());
        $this->getMainPhotoId();
        $this->getRoute();
        $this->getContent();
        $cache->save($this);
    }

    public static function getList($order, $orderType, $content, $route, $searchName, $status, $page, $onPage, $limit, $group = true, $wher = false)
    {
        $return = new stdClass();
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(self::_tableName);
        $select->join('pages', 'pages.idPage = '.self::_tableName.'.idPage');
        if ($route) {
            $select->join('routing', 'pages.idRoute = routing.idRoute');
        }
        if ($content) {
            $select->join('content', 'content.idContentPack = pages.idContentPack');
            $select->where('content.idLanguage = ?', Kernel_Language::getCurrent()->getId());
            if ($searchName) {
                $select->join('fields', "fields.idContent = content.idContent AND fields.fieldName = 'name'");
                $select->where('fields.fieldText LIKE ?', $searchName);
            }
        }
        $select->where('pages.pageType = ?', self::TYPE_POST);
        if ($wher) {
            $select->where($wher);
        }
        if ($order && $orderType) {
            if ($order == 'BY' && $orderType == 'RAND') {
                $select->order(new Zend_Db_Expr('RAND()'));
            } else {
                $select->order($order . ' ' . $orderType);
            }
        } else {
            $select->order('pages.idPage DESC');
        }
        if ($status !== false)
            $select->where('pages.pageStatus = ?', $status);
        if ($group !== false)
            $select->group(self::_tableName.'.id');
        if ($limit !== false)
            $select->limit($limit);
        if ($page !== false) {
            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage($onPage);
            $paginator->setPageRange(5);
            $paginator->setCurrentPageNumber($page);
            $return->paginator = $paginator;
        } else {
            $return->paginator = $db->fetchAll($select);
        }
        $return->data = array();
        $i = 0;
        foreach ($return->paginator as $projectData) {
            $return->data[$i] = self::getSelf($projectData);
            if ($route) {
                $url = new Application_Model_Kernel_Routing_Url($projectData->url);
                $defaultParams = new Application_Model_Kernel_Routing_DefaultParams($projectData->defaultParams);
                $route = new Application_Model_Kernel_Routing($projectData->idRoute, $projectData->type, $projectData->name, $projectData->module, $projectData->controller, $projectData->action, $url, $defaultParams, $projectData->routeStatus);
                $return->data[$i]->setRoute($route);
            }
            if ($content) {
                $contentLang = new Application_Model_Kernel_Content_Language($projectData->idContent, $projectData->idLanguage, $projectData->idContentPack);
                $contentLang->setFieldsArray(Application_Model_Kernel_Content_Fields::getFieldsByIdContent($projectData->idContent));
                $return->data[$i]->setContent($contentLang);
            }
            $i++;
        }

        return $return;
    }

    public function show()
    {
        $this->_pageStatus = self::STATUS_SHOW;
        $this->savePageData();
//        $this->clearCache();
    }

    public function hide()
    {
        $this->_pageStatus = self::STATUS_HIDE;
        $this->savePageData();
//        $this->clearCache();
    }

    public function delete()
    {
        $db = Zend_Registry::get('db');
        $db->delete(self::_tableName, "'.self::_tableName.'.idPage = {$this->_idPage}");
        $this->deletePage();
    }

    public static function changePosition($idPage, $position)
    {
        $db = Zend_Registry::get('db');
        $db->update(self::_tableName, array("projectPosition" => $position), 'idPage = ' . (int)$idPage);

        return true;
    }

    public function getMainPhotoId()
    {
        return $this->mainPhotoId;
    }


    public function getMainPhoto()
    {
        if (is_null($this->mainPhoto))
            $this->mainPhoto = Application_Model_Kernel_Photo::getById($this->getMainPhotoId());

        return $this->mainPhoto;
    }

    public function setMainPhoto(Application_Model_Kernel_Photo &$photo)
    {
        $this->mainPhoto = $photo;

        return $this;
    }

    public function setMainPhotoId($id)
    {
        $this->mainPhotoId = $id;

        return $this;
    }

    public function setPath($data)
    {
        $path = Application_Model_Kernel_TextRedactor::makeTranslit($data->content[1]["name"]);
        $this->getRoute()->setUrl('/'.$path);
    }

    public function setCategoryId($id)
    {
        $this->categoryId = $id;

        return $this;
    }

    public function setCategory(Application_Model_Kernel_Category $category)
    {
        $this->categoryId = null;
        $this->categoryId2 = null;
        $this->categoryId3 = null;

        switch ( $category->getCurrentLevel() ) {
            case 1:
                $this->categoryId = $category->getId();
                break;
            case 2:
                $this->categoryId = $category->getParent()->getId();
                $this->categoryId2 = $category->getId();
                break;
            case 3:
                $this->categoryId = $category->getParent()->getParent()->getId();
                $this->categoryId2 = $category->getParent()->getId();
                $this->categoryId3 = $category->getId();
                break;
        }

        return $this;
    }

    public function getCurrentCategory()
    {
        if (is_null($this->currentCategory)) {
            $id = $this->categoryId;
            if (!is_null($this->categoryId2)) {
                $id = $this->categoryId2;
                if (!is_null($this->categoryId3)) {
                    $id = $this->categoryId3;
                }
            }

            $this->currentCategory = Application_Model_Kernel_Category::getById($id);
        }

        return $this->currentCategory;
    }
}
