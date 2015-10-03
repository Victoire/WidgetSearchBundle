<?php

namespace Victoire\Widget\SearchBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Victoire\Bundle\CoreBundle\Form\WidgetType;
use Victoire\Widget\SearchBundle\Entity\WidgetSearch;


/**
 * WidgetSearch form type
 */
class WidgetSearchType extends WidgetType
{

    /**
     * define form fields
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);
        //add the mode to the form
        $builder->add('emitter', null, array(
                'label' => 'victoire.widget_search.form.emitter.label',
                'attr' => [
                    'data-refreshOnChange' => "true",
                ],
        ))->add('receiver', null, array(
                'label' => 'victoire.widget_search.form.receiver.label',
                'attr' => [
                    'data-refreshOnChange' => "true",
                ],
        ))->add('resultsPage', null, array(
                'label' => 'victoire.widget_search.form.resultsPage.label',
                'empty_value' => true
            ));

    }

    /**
     * bind form to WidgetSearch entity
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'data_class'         => 'Victoire\Widget\SearchBundle\Entity\WidgetSearch',
            'widget'             => 'Search',
            'translation_domain' => 'victoire'
        ));
    }

    /**
     * get form name
     *
     * @return string The form name
     */
    public function getName()
    {
        return 'victoire_widget_form_search';
    }
}
