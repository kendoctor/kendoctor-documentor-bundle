<?php

namespace Kendoctor\Bundle\DocumentorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentVersionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('version')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kendoctor\Bundle\DocumentorBundle\Entity\DocumentVersion'
        ));
    }

    public function getName()
    {
        return 'kendoctor_bundle_documentorbundle_documentversiontype';
    }
}
