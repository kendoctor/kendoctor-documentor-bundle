<?php

namespace Kendoctor\Bundle\DocumentorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * DocumentReleasedVersion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DocumentReleasedVersion
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
     * @var integer
     *
     * @ORM\Column(name="versionNo", type="integer")
     */
    private $versionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="versionDescription", type="string", length=20)
     */
    private $versionDescription;

    /**
     * @var string
     * @Gedmo\Slug(fields={"versionDescription"})
     * @ORM\Column(name="slug", type="string", length=20)
     */
    private $slug;
    
   
    /**
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="versions")
     * 
     * @var type 
     */
    private $document;


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
     * Set versionNo
     *
     * @param integer $versionNo
     * @return DocumentReleasedVersion
     */
    public function setVersionNo($versionNo)
    {
        $this->versionNo = $versionNo;

        return $this;
    }

    /**
     * Get versionNo
     *
     * @return integer 
     */
    public function getVersionNo()
    {
        return $this->versionNo;
    }

    /**
     * Set versionDescription
     *
     * @param string $versionDescription
     * @return DocumentReleasedVersion
     */
    public function setVersionDescription($versionDescription)
    {
        $this->versionDescription = $versionDescription;

        return $this;
    }

    /**
     * Get versionDescription
     *
     * @return string 
     */
    public function getVersionDescription()
    {
        return $this->versionDescription;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return DocumentReleasedVersion
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set document
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\Document $document
     * @return DocumentReleasedVersion
     */
    public function setDocument(\Kendoctor\Bundle\DocumentorBundle\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \Kendoctor\Bundle\DocumentorBundle\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }
}
