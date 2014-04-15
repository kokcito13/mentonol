<?php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Application\Sonata\UserBundle\Entity\User;

/**
 * Site
 *
 * @ORM\Table(name="sites")
 * @ORM\Entity
 */
class Site
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\UserBundle\Entity\User", mappedBy="sites_for_km", cascade={"persist", "remove"} )
     */
    private $keyManagers;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\UserBundle\Entity\User", mappedBy="sites_for_editor", cascade={"persist", "remove"} )
     */
    private $editors;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User", inversedBy="sites", cascade={"persist", "remove"} )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="site")
     */
    private $categories;

    public function __construct()
    {
        $this->keyManagers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->editors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new\DateTime();
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
     * Set name
     *
     * @param string $name
     * @return Site
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Site
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add keyManagers
     *
     * @param \Application\Sonata\UserBundle\Entity\User $keyManagers
     * @return Site
     */
    public function addKeyManager(\Application\Sonata\UserBundle\Entity\User $keyManagers)
    {
        $this->keyManagers[] = $keyManagers;

        return $this;
    }

    /**
     * Remove keyManagers
     *
     * @param \Application\Sonata\UserBundle\Entity\User $keyManagers
     */
    public function removeKeyManager(\Application\Sonata\UserBundle\Entity\User $keyManagers)
    {
        $this->keyManagers->removeElement($keyManagers);
    }

    /**
     * Get keyManagers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKeyManagers()
    {
        return $this->keyManagers;
    }

    /**
     * Set owner
     *
     * @param \Application\Sonata\UserBundle\Entity\User $owner
     * @return Site
     */
    public function setOwner(\Application\Sonata\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add editors
     *
     * @param \Application\Sonata\UserBundle\Entity\User $editors
     * @return Site
     */
    public function addEditor(\Application\Sonata\UserBundle\Entity\User $editors)
    {
        $this->editors[] = $editors;

        return $this;
    }

    /**
     * Remove editors
     *
     * @param \Application\Sonata\UserBundle\Entity\User $editors
     */
    public function removeEditor(\Application\Sonata\UserBundle\Entity\User $editors)
    {
        $this->editors->removeElement($editors);
    }

    /**
     * Get editors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEditors()
    {
        return $this->editors;
    }


    public function setEditors($editors)
    {
        $this->editors = $editors;

        return $this;
    }

    /**
     * Add categories
     *
     * @param \Acme\DemoBundle\Entity\Category $categories
     * @return Site
     */
    public function addCategory(\Acme\DemoBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Acme\DemoBundle\Entity\Category $categories
     */
    public function removeCategory(\Acme\DemoBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public function __toString()
    {
        return __CLASS__;
    }
}
