<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Acme\DemoBundle\Entity\Link;
use Acme\DemoBundle\Entity\Url;

/**
 * Project
 *
 * @ORM\Table(name="projects")
 * @ORM\Entity
 */
class Project
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="project_name", type="string", length=255)
     */
    private $projectName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


    public function __construct()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set projectName
     *
     * @param string $projectName
     * @return Project
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * Get projectName
     *
     * @return string 
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * Set urls
     *
     * @param ArrayCollection|array $urls
     * @return Project
     */
    public function setUrls($urls)
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * Get urls
     *
     * @return ArrayCollection
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * Set links
     *
     * @param ArrayCollection|array $links
     * @return Project
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Get links
     *
     * @return ArrayCollection|array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Project
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Project
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getPercentStatus()
    {
        $urls = $this->getUrls();
        $count = $urls->count();
        $countNotOn = 0;
        $prcnt = 0;
        foreach ($urls as $url) {
            if ($url->getStatus() == Url::STATUS_DONE) {
                $countNotOn++;
            }
        }
        if ($countNotOn > 0)
            $prcnt = $countNotOn/($count/100);

        return (int)$prcnt;
    }

    public function getLinksText()
    {
        return $this->getLinksType(Link::TYPE_TEXT);
    }

    public function getLinksAfterText()
    {
        return $this->getLinksType(Link::TYPE_AFTER_TEXT);
    }

    public function getLinksType($type)
    {
        $ar = array();

        foreach ($this->getLinks() as $link) {
            if ($link->getType() == $type) {
                $ar[] = $link;
            }
        }

        return $ar;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Project
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set sitemap
     *
     * @param string $sitemap
     * @return Project
     */
    public function setSitemap($sitemap)
    {
        $this->sitemap = $sitemap;

        return $this;
    }

    /**
     * Get sitemap
     *
     * @return string 
     */
    public function getSitemap()
    {
        return $this->sitemap;
    }

    /**
     * Add urls
     *
     * @param Url $urls
     * @return Project
     */
    public function addUrl(Url $urls)
    {
        $this->urls[] = $urls;

        return $this;
    }

    /**
     * Remove urls
     *
     * @param Url $urls
     */
    public function removeUrl(Url $urls)
    {
        $this->urls->removeElement($urls);
    }

    /**
     * Add links
     *
     * @param Link $links
     * @return Project
     */
    public function addLink(Link $links)
    {
        $this->links[] = $links;

        return $this;
    }

    /**
     * Remove links
     *
     * @param Link $links
     */
    public function removeLink(Link $links)
    {
        $this->links->removeElement($links);
    }
}
