<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 3/15/14
 * Time: 2:20 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Acme\DemoBundle\Service;

use Doctrine\ORM\EntityManager;
use Acme\DemoBundle\Entity\Url;
use Acme\DemoBundle\Entity\Link as NewLink;
use Acme\DemoBundle\Entity\Project;

class Link
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Method cheking url for keys
     */
    public function check()
    {
        try {
            $query = $this->em->createQueryBuilder()
                ->select('u')
                ->from('AcmeDemoBundle:Url', 'u')
                ->leftJoin('u.project', 'p')
                ->where('u.status = ' . Url::STATUS_WAIT)
                ->andWhere('p.status = ' . Project::STATUS_ACTIVE)
                ->setMaxResults(20)
                ->getQuery();
            $uls   = $query->getResult();

            foreach ($uls as $v) {
                $this->getFromUrl($v->getProject(), $v->getUrl());
                $v->setUpdatedAt(new \DateTime());
                $v->setStatus(Url::STATUS_DONE);

                $this->em->persist($v);
                $this->em->flush();
            }
        } catch (Exception $e) {
            mail('oklosovich@i.ua','Error - Tags.hippl.net', $e->getMessage());
        }
    }

    public function getFromUrl($project, $url)
    {
        $url = "http://perelink.binet.pro/service/otvet.php?proj=" . $project->getProjectName() . "&url=" . $url;

        $file    = file_get_contents(trim($url));
        $content = json_decode($file);

        $array = $this->setAsArray($content, $project);

        return $array;
    }

    public function setAsArray($cont, $project)
    {
        $array = array ();
        foreach ($cont as $k) {
            $k[0] = trim($k[0]);
            $k[1] = trim($k[1]);
            $k[2] = trim($k[2]);
            $k[3] = trim($k[3]);
            if ($k[3] == "text") {
                $link = $this->em->getRepository('AcmeDemoBundle:Link')->findOneBy(array(
                                                                                     'name' => $k[2],
                                                                                     'url' => $k[1],
                                                                                     'project' => $project,
                                                                                     'type' => NewLink::TYPE_TEXT
                                                                                ));
                if (!$link) {
                    $link = new NewLink();
                }
                $link->setProject($project);
                $link->setName($k[2]);
                $link->setUrl($k[1]);
                $link->setUrlIn($k[0]);
                $link->setType(NewLink::TYPE_TEXT);
                $link->setUpdatedAt(new \DateTime());

                $this->em->persist($link);
                $this->em->flush();
            } else if ($k[3] == "posle") {
                $link = $this->em->getRepository('AcmeDemoBundle:Link')->findOneBy(array(
                                                                                        'name' => $k[2],
                                                                                        'url' => $k[1],
                                                                                        'project' => $project,
                                                                                        'type' => NewLink::TYPE_AFTER_TEXT,
                                                                                        'urlIn' => $k[0]
                                                                                   ));
                if (!$link) {
                    $link = new NewLink();
                }

                $link->setProject($project);
                $link->setName($k[2]);
                $link->setUrl($k[1]);
                $link->setUrlIn($k[0]);
                $link->setType(NewLink::TYPE_AFTER_TEXT);
                $link->setUpdatedAt(new \DateTime());

                $this->em->persist($link);
                $this->em->flush();
            }
        }

        return $array;
    }
}