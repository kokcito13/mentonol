<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\DemoBundle\Entity\Project;
use Acme\DemoBundle\Entity\Url;
use Acme\DemoBundle\Entity\Link;
use Acme\DemoBundle\Form\ProjectType;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Project controller.
 *
 * @Route("/boss/project")
 */
class ProjectController extends Controller
{

    /**
     * Lists all Project entities.
     *
     * @Route("/", name="admin_project")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user_id = $this->getUser()->getId();

        $entities = $em->getRepository('AcmeDemoBundle:Project')->findBy(array(
                                                                              'status'=>Project::STATUS_ACTIVE,
                                                                              'userId' => $user_id
                                                                         ));

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Project entity.
     *
     * @Route("/", name="admin_project_create")
     * @Method("POST")
     * @Template("AcmeDemoBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $user_id = $this->getUser()->getId();

        $entity = new Project();
        $entity->setUserId($user_id);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->generateUrlsFromSitemap($entity);

            return $this->redirect($this->generateUrl('admin_project_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    public function getLinksFromXml($uri)
    {
        $arr =array();
        $xml = new \SimpleXMLElement(file_get_contents($uri));
        foreach ($xml->url as $url_list) {
            $arr[] = $url_list->loc;
        }

        return $arr;
    }

    public function generateUrlsFromSitemap($project)
    {
        $url = array();
        $em = $this->getDoctrine()->getManager();
        $siteMap = $project->getSitemap();
        $urls = $this->getLinksFromXml($siteMap);
        foreach($urls as $k=>$v) {
            $url[$k] = new Url();
            $url[$k]->setUrl($v);
            $url[$k]->setProject($project);
            $em->persist($url[$k]);
        }
        $em->flush();
    }

    /**
    * Creates a form to create a Project entity.
    *
    * @param Project $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('admin_project_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="admin_project_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Project();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/refresh", name="admin_project_refresh")
     * @Method("GET")
     */
    public function refreshAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_id = $this->getUser()->getId();

        /** @var $entity Project */
        $entity = $em->getRepository('AcmeDemoBundle:Project')->findOneBy(
            array(
                 'id' => $id,
                 'userId' => $user_id
            )
        );

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        foreach ($entity->getUrls() as $url) {
            $em->remove($url);
        }
        foreach ($entity->getLinks() as $link) {
            $em->remove($link);
        }
        $em->flush();

        $this->generateUrlsFromSitemap($entity);

        return $this->redirect($this->generateUrl('admin_project_show', array('id' => $entity->getId())));
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}", name="admin_project_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeDemoBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}", name="admin_project_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AcmeDemoBundle:Project')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Project entity.');
            }

            $entity->setStatus(Project::STATUS_DELETE);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_project'));
    }

    /**
     * Creates a form to delete a Project entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_project_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * @Route("/file/{id}/t.txt", name="admin_project_file")
     * @Method("GET")
     * @Template()
     */
    public function showFileAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeDemoBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }


        return array(
            'entity'      => $entity,
        );
    }

    /**
     * @Route("/file/{id}/after_t.txt", name="admin_project_file_after_text")
     * @Method("GET")
     * @Template()
     */
    public function showFileAfterAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcmeDemoBundle:Link')->findBy(array(
                                                                           'project' =>$id,
                                                                           'type' => Link::TYPE_AFTER_TEXT,
                                                                      ), array(
                                                                              'urlIn' => 'DESC'
                                                                         ));

        return array(
            'entities'      => $entities,
        );
    }
}
