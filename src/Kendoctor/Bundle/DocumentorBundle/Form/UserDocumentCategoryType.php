<?php

namespace Kendoctor\Bundle\DocumentorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserDocumentCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('parent')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kendoctor\Bundle\DocumentorBundle\Entity\UserDocumentCategory'
        ));
    }

    public function getName()
    {
        return 'kendoctor_bundle_documentorbundle_userdocumentcategorytype';
    }
}
