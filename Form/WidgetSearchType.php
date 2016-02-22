<?php

namespace Victoire\Widget\SearchBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Victoire\Bundle\CoreBundle\Form\WidgetType;
use Victoire\Widget\SearchBundle\Entity\WidgetSearch;

/**
 * WidgetSearch form type.
 */
class WidgetSearchType extends WidgetType
{
    /**
     * define form fields.
     *
     * @param FormBuilderInterface $builder
     *
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        //add the mode to the form
        $builder->add('emitter', null, [
            'label' => 'victoire.widget_search.form.emitter.label',
            'attr'  => [
                'data-refreshOnChange' => 'true',
            ],
        ])->add('receiver', null, [
            'label' => 'victoire.widget_search.form.receiver.label',
            'attr'  => [
                'data-refreshOnChange' => 'true',
            ],
        ])->add('resultsPage', null, [
            'label'       => 'victoire.widget_search.form.resultsPage.label',
            'empty_value' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class'         => 'Victoire\Widget\SearchBundle\Entity\WidgetSearch',
            'widget'             => 'Search',
            'translation_domain' => 'victoire',
        ]);
    }
}
