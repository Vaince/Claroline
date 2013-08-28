<?php

namespace ICAP\DropZoneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DropZoneCommonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('instruction', 'textarea', array('required' => false))

            ->add('allowWorkspaceResource', 'checkbox', array('required' => false))
            ->add('allowUpload', 'checkbox', array('required' => false))
            ->add('allowUrl', 'checkbox', array('required' => false))

            ->add('peerReview', 'choice', array(
                'choices' => array(
                    false => 'Standard evaluation',
                    true => 'Peer review evaluation'
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('expectedTotalCorrection', 'number', array('required' => false))
            ->add('allowDropInReview', 'checkbox', array('required' => false))

            ->add('displayNotationToLearners', 'checkbox', array('required' => false))
            ->add('displayNotationMessageToLearners', 'checkbox', array('required' => false))
            ->add('minimumScoreToPass', 'number', array('required' => false))

            ->add('manualPlanning', 'choice', array(
                'required' => true,
                'choices' => array(
                    true => 'manualPlanning',
                    false => 'sheduleByDate'
                ),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('manualState', 'choice', array(
                'choices' => array(
                    'notStarted' => 'manualStateNotStarted',
                    'allowDrop' => 'allowDropManualState',
                    'peerReview' => 'peerReviewManualState'
                ),
                'expanded' => true,
                'multiple' => false
            ))

            ->add('startAllowDrop', 'text', array('required' => false))
            ->add('endAllowDrop', 'text', array('required' => false))
            ->add('endReview', 'text', array('required' => false));
    }

    public function getName()
    {
        return 'icap_dropzone_common_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }
}