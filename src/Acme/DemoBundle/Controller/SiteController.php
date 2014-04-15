<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\DemoBundle\Entity\Site;
use Acme\DemoBundle\Form\SiteType;

use Application\Sonata\UserBundle\Entity\User;

/**
 * Site controller.
 *
 * @Route("/boss/site")
 */
class SiteController extends Controller
{

    /**
     * Lists all Site entities.
     *
     * @Route("/", name="boss_site")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcmeDemoBundle:Site')->findBy(array(
                                                                           'owner' => $this->getUser()
                                                                      ));

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Site entity.
     *
     * @Route("/", name="boss_site_create")
     * @Method("POST")
     * @Template("AcmeDemoBundle:Site:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Site();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setOwner($this->getUser());

            $em->persist($entity);
            foreach ($entity->getEditors() as $ed) { /** @var $ed User */
                $ed->addSitesForEditor($entity);
                $em->persist($ed);
            }
            foreach ($entity->getKeyManagers() as $key) { /** @var $key User */
                $key->addSitesForKm($entity);
                $em->persist($key);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('boss_site_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Site entity.
    *
    * @param Site $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Site $entity)
    {
        $form = $this->createForm(new SiteType(), $entity, array(
            'action' => $this->generateUrl('boss_site_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Site entity.
     *
     * @Route("/new", name="boss_site_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Site();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Site entity.
     *
     * @Route("/{id}", name="boss_site_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeDemoBundle:Site')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Site entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Site entity.
     *
     * @Route("/{id}/edit", name="boss_site_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcmeDemoBundle:Site')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Site entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Site entity.
    *
    * @param Site $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Site $entity)
    {
        $form = $this->createForm(new SiteType(), $entity, array(
            'action' => $this->generateUrl('boss_site_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Site entity.
     *
     * @Route("/{id}", name="boss_site_update")
     * @Method("PUT")
     * @Template("AcmeDemoBundle:Site:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var $entity Site */
        $entity = $em->getRepository('AcmeDemoBundle:Site')->find($id);
        foreach ($entity->getEditors() as $ed) { /** @var $ed User */
            $ed->removeSitesForEditor($entity);
        }
        foreach ($entity->getKeyManagers() as $key) { /** @var $key User */
            $key->removeSitesForKm($entity);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Site entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            foreach ($entity->getEditors() as $ed) { /** @var $ed User */
                $ed->addSitesForEditor($entity);
                $em->persist($ed);
            }
            foreach ($entity->getKeyManagers() as $key) { /** @var $key User */
                $key->addSitesForKm($entity);
                $em->persist($key);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('boss_site_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Site entity.
     *
     * @Route("/{id}", name="boss_site_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AcmeDemoBundle:Site')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Site entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('boss_site'));
    }

    /**
     * Creates a form to delete a Site entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('boss_site_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
