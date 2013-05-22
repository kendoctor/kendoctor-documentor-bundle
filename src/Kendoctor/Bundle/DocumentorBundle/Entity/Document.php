<?php

namespace Kendoctor\Bundle\DocumentorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kendoctor\Bundle\DocumentorBundle\Entity\Content;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Document extends Content
{

    /**
     *@ORM\ManyToOne(targetEntity="UserDocumentCategory", inversedBy="documents")
     * @var type 
     */
    private $category;
    

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
}