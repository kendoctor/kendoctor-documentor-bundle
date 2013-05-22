<?php

namespace Kendoctor\Bundle\DocumentorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kendoctor\Bundle\DocumentorBundle\Entity\Category;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UserDocumentCategory
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 */
class UserDocumentCategory extends Category
{
   
    
     /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="category")
     * 
     * @var type
     */
    private $documents;

  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add documents
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\Document $documents
     * @return UserDocumentCategory
     */
    public function addDocument(\Kendoctor\Bundle\DocumentorBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;
    
        return $this;
    }

    /**
     * Remove documents
     *
     * @param \Kendoctor\Bundle\DocumentorBundle\Entity\Document $documents
     */
    public function removeDocument(\Kendoctor\Bundle\DocumentorBundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}