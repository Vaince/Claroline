<?php

namespace ICAP\DropZoneBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CorrectionCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $params = array('required' => true);

        if ($options['edit'] === false) {
            $params['read_only'] = 'true';
        }
        $builder->add('comment', 'textarea', $params);
    }

    public function getName()
    {
        return 'icap_dropzone_correct_comment_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'edit' => true,
        ));
    }
}