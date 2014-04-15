<?php

namespace Acme\ManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Task
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
     * @var string
     *
     * @ORM\Column(name="main_keys", type="text")
     */
    private $mainKeys;

    /**
     * @var string
     *
     * @ORM\Column(name="help_keys", type="text")
     */
    private $helpKeys;

    /**
     * @var string
     *
     * @ORM\Column(name="diluted_keys", type="text")
     */
    private $dilutedKeys;

    /**
     * @var string
     *
     * @ORM\Column(name="not_use_keys", type="text")
     */
    private $notUseKeys;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="text_long_from", type="integer")
     */
    private $textLongFrom;

    /**
     * @var integer
     *
     * @ORM\Column(name="text_long_to", type="integer")
     */
    private $textLongTo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User", inversedBy="tasks", cascade={"persist", "remove"} )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $owner;

    
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
     * @return Task
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
     * Set mainKeys
     *
     * @param string $mainKeys
     * @return Task
     */
    public function setMainKeys($mainKeys)
    {
        $this->mainKeys = $mainKeys;

        return $this;
    }

    /**
     * Get mainKeys
     *
     * @return string 
     */
    public function getMainKeys()
    {
        return $this->mainKeys;
    }

    /**
     * Set helpKeys
     *
     * @param string $helpKeys
     * @return Task
     */
    public function setHelpKeys($helpKeys)
    {
        $this->helpKeys = $helpKeys;

        return $this;
    }

    /**
     * Get helpKeys
     *
     * @return string 
     */
    public function getHelpKeys()
    {
        return $this->helpKeys;
    }

    /**
     * Set dilutedKeys
     *
     * @param string $dilutedKeys
     * @return Task
     */
    public function setDilutedKeys($dilutedKeys)
    {
        $this->dilutedKeys = $dilutedKeys;

        return $this;
    }

    /**
     * Get dilutedKeys
     *
     * @return string 
     */
    public function getDilutedKeys()
    {
        return $this->dilutedKeys;
    }

    /**
     * Set notUseKeys
     *
     * @param string $notUseKeys
     * @return Task
     */
    public function setNotUseKeys($notUseKeys)
    {
        $this->notUseKeys = $notUseKeys;

        return $this;
    }

    /**
     * Get notUseKeys
     *
     * @return string 
     */
    public function getNotUseKeys()
    {
        return $this->notUseKeys;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Task
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
     * Set status
     *
     * @param boolean $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set textLongFrom
     *
     * @param integer $textLongFrom
     * @return Task
     */
    public function setTextLongFrom($textLongFrom)
    {
        $this->textLongFrom = $textLongFrom;

        return $this;
    }

    /**
     * Get textLongFrom
     *
     * @return integer 
     */
    public function getTextLongFrom()
    {
        return $this->textLongFrom;
    }

    /**
     * Set textLongTo
     *
     * @param integer $textLongTo
     * @return Task
     */
    public function setTextLongTo($textLongTo)
    {
        $this->textLongTo = $textLongTo;

        return $this;
    }

    /**
     * Get textLongTo
     *
     * @return integer 
     */
    public function getTextLongTo()
    {
        return $this->textLongTo;
    }

    /**
     * Set owner
     *
     * @param \Application\Sonata\UserBundle\Entity\User $owner
     * @return Task
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
}
