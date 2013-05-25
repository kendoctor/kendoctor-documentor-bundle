<?php

namespace Kendoctor\Bundle\DocumentorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentEditingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category')
            ->add('title', 'hidden')
            ->add('body', 'hidden' )
            ->add('locale','hidden')
            ->add('currentVersionHash', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kendoctor\Bundle\DocumentorBundle\Entity\Document'
        ));
    }

    public function getName()
    {
        return 'kendoctor_bundle_documentorbundle_documenttype';
    }
}
