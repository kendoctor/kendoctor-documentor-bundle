<?php

namespace Kendoctor\Bundle\DocumentorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Kendoctor\Bundle\DocumentorBundle\Entity\Content;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * Document
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Kendoctor\Bundle\DocumentorBundle\Entity\DocumentRepository")
 */
class Document extends Content
{

    /**
     *@ORM\ManyToOne(targetEntity="UserDocumentCategory", inversedBy="documents")
     * @var type 
     */
    private $category;
    
    /**
     * @ORM\OneToMany(targetEntity="DocumentReleasedVersion", mappedBy="document")
     * 
     * @var type
     */
    private $versions;

    /**
     * @ORM\OneToMany(targetEntity="DocumentVersion", mappedBy="document", cascade={"all"})
     * 
     * @var type
     */
    private $versionLogs;
    
    /**
     * Set category
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\UserDocumentCategory $category
     * @return Document
     */
    public function setCategory(\Kendoctor\Bundle\DocumentorBundle\Entity\UserDocumentCategory $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Kendoctor\Bundle\DocumentorBundle\Entity\UserDocumentCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }
 
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->currentVersionHash = md5(uniqid(rand(), true));
    }

 
    
    /**
     * @ORM\PostLoad
     */
    public function generateHash()
    {
       // $this->currentVersionHash = md5(uniqid(rand(), true));
    }

    /**
     * Add versions
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\DocumentReleasedVersion $versions
     * @return Document
     */
    public function addVersion(\Kendoctor\Bundle\DocumentorBundle\Entity\DocumentReleasedVersion $versions)
    {
        $this->versions[] = $versions;

        return $this;
    }

    /**
     * Remove versions
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\DocumentReleasedVersion $versions
     */
    public function removeVersion(\Kendoctor\Bundle\DocumentorBundle\Entity\DocumentReleasedVersion $versions)
    {
        $this->versions->removeElement($versions);
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVersions()
    {
        return $this->versions;
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
     * Add versionLogs
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersion $versionLogs
     * @return Document
     */
    public function addVersionLog(\Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersion $versionLog)
    {
        $versionLog->setDocument($this);
        $this->versionLogs[] = $versionLog;

        return $this;
    }

    /**
     * Remove versionLogs
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersion $versionLogs
     */
    public function removeVersionLog(\Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersion $versionLogs)
    {
        $this->versionLogs->removeElement($versionLogs);
    }

    /**
     * Get versionLogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVersionLogs()
    {
        return $this->versionLogs;
    }
}
