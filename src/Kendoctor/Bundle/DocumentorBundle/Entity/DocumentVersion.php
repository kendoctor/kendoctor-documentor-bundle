<?php

namespace Kendoctor\Bundle\DocumentorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * DocumentVersion
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersionRepository")
 */
class DocumentVersion {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
 
    
    /**
     * @ORM\Column(type="string", length=32)
     * @var type 
     */
    private $documentHash;
    
    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=10)
     */
    private $version;

    /**
     * @var string
     * 
     * @Gedmo\Slug(fields={"version"})
     * @ORM\Column(name="versionSlug", type="string", length=10)
     */
    private $versionSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=10)
     */
    private $lang;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    
    /**
     *
     * @var type 
     * 
     * @ORM\Column(name="isReleased", type="boolean")
     */
    private $isReleased;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Document", inversedBy="versionLogs")
     * 
     * @var type 
     */
    private $document;

    public function __construct() {
        $this->versionSlug = $this->version = md5(uniqid(rand(), true));
        $this->isReleased = false;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return DocumentVersion
     */
    public function setVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Set versionSlug
     *
     * @param string $versionSlug
     * @return DocumentVersion
     */
    public function setVersionSlug($versionSlug) {
        $this->versionSlug = $versionSlug;

        return $this;
    }

    /**
     * Get versionSlug
     *
     * @return string 
     */
    public function getVersionSlug() {
        return $this->versionSlug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return DocumentVersion
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return DocumentVersion
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return DocumentVersion
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return DocumentVersion
     */
    public function setLang($lang) {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Set document
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\Document $document
     * @return DocumentVersion
     */
    public function setDocument(\Kendoctor\Bundle\DocumentorBundle\Entity\Document $document = null) {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \Kendoctor\Bundle\DocumentorBundle\Entity\Document 
     */
    public function getDocument() {
        return $this->document;
    }


    /**
     * Set isReleased
     *
     * @param boolean $isReleased
     * @return DocumentVersion
     */
    public function setIsReleased($isReleased)
    {
        $this->isReleased = $isReleased;

        return $this;
    }

    /**
     * Get isReleased
     *
     * @return boolean 
     */
    public function getIsReleased()
    {
        return $this->isReleased;
    }

    /**
     * Set documentHash
     *
     * @param string $documentHash
     * @return DocumentVersion
     */
    public function setDocumentHash($documentHash)
    {
        $this->documentHash = $documentHash;

        return $this;
    }

    /**
     * Get documentHash
     *
     * @return string 
     */
    public function getDocumentHash()
    {
        return $this->documentHash;
    }
}
