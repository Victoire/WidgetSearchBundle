<?php

namespace Victoire\Widget\SearchBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Victoire\Bundle\CoreBundle\Form\WidgetType;
use Victoire\Widget\SearchBundle\Entity\WidgetSearch;

/**
 * WidgetSearch form type.
 */
class WidgetSearchType extends WidgetType
{
    /**
     * Define form fields.
     *
     * @param FormBuilderInterface $builder
     *
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        //add the mode to the form
        $builder->add('resultsPage', null, [
            'label'       => 'victoire.widget_search.form.resultsPage.label',
        ])->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            self::manageEmitterReceiver($form);
        });
    }

    /**
     * Manage emitter and receiver fields.
     *
     * @param FormInterface $form
     */
    protected function manageEmitterReceiver($form)
    {
        $form->add('receiver', null, [
            'label' => 'victoire.widget_search.form.receiver.label',
            'attr'  => [
                'data-refreshOnChange' => 'true',
                'data-target'          => 'form[name="'.$form->getRoot()->getName().'"]',
                'data-update-strategy' => 'replaceWith',
            ],
        ])->add('emitter', null, [
            'label' => 'victoire.widget_search.form.emitter.label',
            'attr'  => [
                'data-refreshOnChange' => 'true',
                'data-target'          => 'form[name="'.$form->getRoot()->getName().'"]',
                'data-update-strategy' => 'replaceWith',
            ],
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
