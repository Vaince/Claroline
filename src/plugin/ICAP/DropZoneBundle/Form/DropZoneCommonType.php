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
                'required' => true,
                'choices' => array(
                    false => 'Standard evaluation',
                    true => 'Peer review evaluation'
                ),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('expectedTotalCorrection', 'number', array('required' => true))
            ->add('allowDropInReview', 'checkbox', array('required' => false))

            ->add('displayNotationToLearners', 'checkbox', array('required' => false))
            ->add('displayNotationMessageToLearners', 'checkbox', array('required' => false))
            ->add('minimumScoreToPass', 'number', array('required' => true))

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
                    'peerReview' => 'peerReviewManualState',
                    'finished' => 'peerReviewManualState',
                ),
                'expanded' => true,
                'multiple' => false
            ))

//            ->add('startAllowDrop', 'text', array('required' => false))
//            ->add('endAllowDrop', 'text', array('required' => false))
//            ->add('endReview', 'text', array('required' => false))

            ->add('startAllowDrop', 'datetime', array('date_widget' => 'single_text', 'time_widget' => 'single_text', 'with_seconds' => false, 'required' => false))
            ->add('endAllowDrop', 'datetime', array('date_widget' => 'single_text', 'time_widget' => 'single_text', 'with_seconds' => false, 'required' => false))
            ->add('endReview', 'datetime', array('date_widget' => 'single_text', 'time_widget' => 'single_text', 'with_seconds' => false, 'required' => false))
//            ->add('startAllowDrop', 'datetimepicker', array('required' => false, 'time_widget' => 'single_text', 'with_seconds' => false, 'language' => $options['language']))
//            ->add('endAllowDrop', 'datetimepicker', array('required' => false, 'language' => $options['language']))
//            ->add('endReview', 'datetimepicker', array('required' => false, 'language' => $options['language']))
        ;
    }

    public function getName()
    {
        return 'icap_dropzone_common_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'language'           => 'en'
            )
        );
    }
}