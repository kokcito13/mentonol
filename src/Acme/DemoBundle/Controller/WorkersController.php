<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Form\ContactType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Application\Sonata\UserBundle\Entity\User;

class WorkersController extends Controller
{
    const ROLE_KEY_MANAGER = 1;
    const ROLE_EDITOR = 2;
    const ROLE_AUTHOR = 3;

    /**
     * @Route("/boss/workers/{type}", name="boss_key_manager")
     * @Template()
     */
    public function listAction($type)
    {
        $role = $this->getRoleFromVal($type);
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT u FROM ApplicationSonataUserBundle:User u WHERE u.roles LIKE :role AND u.parent = :par'
            )
            ->setParameter('role', '%'.$role.'%')
            ->setParameter('par', $this->getUser()->getId())
        ;

        $users = $query->getResult();

        return array(
            'users' => $users,
            'type' => $type,
            'role' => $this->getRoleNameFromVal($type)
        );
    }

    /**
     * @Route("/boss/workers/{id}/{type}", name="boss_key_manager_edit")
     * @Template()
     */
    public function editAction(Request $request, $id, $type)
    {
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($id) {
            $user = $em->getRepository('ApplicationSonataUserBundle:User')->find($id);
        } else {
            $user = new User();
        }

        if ($request->isMethod('POST')) {
            $user->setParent($currentUser);
            $data = $request->request->all();
            $username = trim($data['username']);
            $email = trim($data['email']);
            $password = trim($data['password']);

            $user->setUsername($username);
            $user->setEmail($email);
            if (!empty($password)){
                $user->setPlainPassword($password);
            }

            $user->setRoles(array($this->getRoleFromVal($type)));
            $user->setEnabled($data['status']);

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('boss_key_manager', array('type' => $type)));
        }

        return array(
            'id' => $id,
            'user' => $user,
            'type' => $type,
            'role' => $this->getRoleNameFromVal($type)
        );
    }

    public function getRoleFromVal($type)
    {
        $role = 'ROLE_KEY_MANAGER';
        switch ($type) {
            case self::ROLE_KEY_MANAGER:
                $role = 'ROLE_KEY_MANAGER';
                break;
            case self::ROLE_EDITOR:
                $role = 'ROLE_EDITOR';
                break;
            case self::ROLE_AUTHOR:
                $role = 'ROLE_AUTHOR';
                break;
        }

        return $role;
    }

    public function getRoleNameFromVal($type)
    {
        $role = 'Key менеджер';
        switch ($type) {
            case self::ROLE_KEY_MANAGER:
                $role = 'Key менеджер';
                break;
            case self::ROLE_EDITOR:
                $role = 'Редактор';
                break;
            case self::ROLE_AUTHOR:
                $role = 'Автор';
                break;
        }

        return $role;
    }
}
