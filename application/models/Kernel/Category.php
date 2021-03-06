<?php

class Application_Model_Kernel_Category extends Application_Model_Kernel_Page
{
    private $id;
    private $parent_id = null;

    private $parent = null;
    private $children = array();

    const _tableName = 'category';

    public function __construct(
        $id,
        $idPage, $idRoute, $idContentPack,
        $pageEditDate, $pageStatus, $position,
        $parentId = null
    )
    {
        parent::__construct($idPage, $idRoute, $idContentPack, $pageEditDate, $pageStatus, self::TYPE_CATEGORY, $position);
        $this->id = $id;
        $this->parent_id = $parentId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function save()
    {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();
        try {
            $db->beginTransaction();
            $insert = is_null($this->_idPage);
            $this->savePageData();
            $data = array (
                'idPage' => $this->getIdPage(),
                'parent_id' => $this->parent_id
            );
            if ($insert) {
                $db->insert(self::_tableName, $data);
                $this->id = $db->lastInsertId();
            } else {
                $db->update(self::_tableName, $data, 'id = ' . intval($this->id));
            }
            $db->commit();
//            $this->clearCache();
        } catch (Exception $e) {
            $db->rollBack();
            Application_Model_Kernel_ErrorLog::addLogRow(Application_Model_Kernel_ErrorLog::ID_SAVE_ERROR, $e->getMessage(), ';Category.php');
            throw new Exception($e->getMessage());
        }
    }

    private function clearCache()
    {
        if (!is_null($this->getidProject())) {
            $cachemanager = Zend_Registry::get('cachemanager');
            $cache        = $cachemanager->getCache('product');
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
                        $data->idPage, $data->idRoute, $data->idContentPack,
                        $data->pageEditDate, $data->pageStatus, $data->position, $data->parent_id);
    }

    public static function loadCache($id)
    {
        $cachemanager = Zend_Registry::get('cachemanager');
        $cache        = $cachemanager->getCache('project');

        return $cache->load($id);
    }

    public static function getById($id)
    {
        $id = (int)$id;
//		$cachemanager = Zend_Registry::get('cachemanager');
//		$cache = $cachemanager->getCache('department');
//		if (($project = $cache->load($idProject)) !== false) {
//			return $project;
//		} else {
        $db     = Zend_Registry::get('db');
        $select = $db->select()->from(self::_tableName);
        $select->join('pages', self::_tableName . '.idPage = pages.idPage');
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

        $db     = Zend_Registry::get('db');
        $select = $db->select()->from(self::_tableName);
        $select->join('pages', self::_tableName . '.idPage = pages.idPage');
        $select->where('pages.idPage = ?', $idPage);
        $select->limit(1);
        if (($productData = $db->fetchRow($select)) !== false) {
            return self::getSelf($productData);
        } else {
            throw new Exception(self::ERROR_INVALID_ID);
        }
    }

    public function completelyCache()
    {
        $cachemanager = Zend_Registry::get('cachemanager');
        $cache        = $cachemanager->getCache('product');
        $cache->load($this->getIdPage());
        $this->getRoute();
        $this->getContent();
        $cache->save($this);
    }

    public static function getList($order, $orderType, $content, $route, $searchName, $status, $page, $onPage, $limit, $group = true, $wher = false)
    {
        $return = new stdClass();
        $db     = Zend_Registry::get('db');
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
        $select->where('pages.pageType = ?', self::TYPE_CATEGORY);
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
            $select->group(self::_tableName . '.id');
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
        $return->data = array ();
        $i            = 0;
        foreach ($return->paginator as $projectData) {
            $return->data[$i] = self::getSelf($projectData);
            if ($route) {
                $url           = new Application_Model_Kernel_Routing_Url($projectData->url);
                $defaultParams = new Application_Model_Kernel_Routing_DefaultParams($projectData->defaultParams);
                $route         = new Application_Model_Kernel_Routing($projectData->idRoute, $projectData->type, $projectData->name, $projectData->module, $projectData->controller, $projectData->action, $url, $defaultParams, $projectData->routeStatus);
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

    public function setParentId($id)
    {
        $this->parent_id = $id;

        return $this;
    }

    public function getParentId()
    {
        return $this->parent_id;
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
        $db->delete(self::_tableName, self::_tableName . ".idPage = {$this->_idPage}");
        $this->deletePage();

        foreach ($this->getChildren()->data as $child) {
            $child->delete();
        }
    }

    public static function changePosition($idPage, $position)
    {
        $db = Zend_Registry::get('db');
        $db->update(self::_tableName, array ("projectPosition" => $position), 'idPage = ' . (int)$idPage);

        return true;
    }

    public function setPath($data)
    {
        $path = Application_Model_Kernel_TextRedactor::makeTranslit($data->content[1]["name"]);
        $this->getRoute()->setUrl('/' . $path);
    }

    public function getParent()
    {
        if (is_null($this->parent) && !is_null($this->parent_id)) {
            $this->parent = self::getById($this->getParentId());
        }

        return $this->parent;
    }

    public function getCurrentLevel()
    {
        $lvl = 1;
        if (!is_null($this->getParent())) {
            $lvl = 2;
            if (!is_null($this->getParent()->getParent())) {
                $lvl = 3;
            }
        }

        return $lvl;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChildren()
    {
        if (empty($this->children)) {
            $this->children = Application_Model_Kernel_Category::getList(false, false, true, true, false, false, false, false, false, true, 'parent_id = '.$this->getId());
            foreach ($this->children->data as $category) {
                $category->setParent($this);
            }
        }

        return $this->children;
    }

    public static function getMainCategoryList()
    {
        return self::getList(false, false, true, false, false, false, false, false, false, true, 'parent_id is NULL');
    }
}
