<?php

namespace ICAP\DropZoneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CorrectCriteriaPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $criteria = $options['criteria'];
        $totalChoice = $options['totalChoice'];

        $choices = array();
        for ($i = 0; $i < $totalChoice; $i++) {
            $choices[$i] = $i;
        }

        foreach ($criteria as $criterion) {
            $builder->add($criterion->getId(), 'choice', array(
                'choices' => $choices,
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label' => $criterion->getInstruction(),
                'label_attr' => array('style' => 'font-weight: normal;')
            ));
        }
    }

    public function getName()
    {
        return 'icap_dropzone_correct_criteria_page_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'totalChoice' => 5,
            'criteria' => array(),
        ));
    }
}