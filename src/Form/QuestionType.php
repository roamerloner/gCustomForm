<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Single-line String' => 'single_line',
                    'Multiple-line Text' => 'multi_line',
                    'Positive Integer' => 'positive_integer',
                    'Checkbox' => 'checkbox',
                    'Multiple Choice' => 'multiple_choice',
                    'Dropdown' => 'dropdown',
                ],
            ])
            ->add('options', TextareaType::class, [
                'label' => 'Options (comma-separated)',
                'required' => false,
                'mapped' => false, // Don’t map to the entity by default; we’ll handle it conditionally
            ])
            ->add('displayInTable', CheckboxType::class, [
                'label' => 'Show in response table',
                'required' => false,
            ]);

        // Use FormEvents to dynamically add/remove the options field
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $question = $event->getData();
            $form = $event->getForm();

            // Remove or add the options field based on the type
            if ($question && !in_array($question->getType(), ['multiple_choice', 'dropdown'])) {
                $form->remove('options');
            } elseif ($question && in_array($question->getType(), ['multiple_choice', 'dropdown'])) {
                $form->add('options', TextareaType::class, [
                    'label' => 'Options (comma-separated)',
                    'required' => false,
                    'mapped' => true, // Map to the entity now that we know it’s needed
                ]);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // Remove or add the options field based on the submitted type
            if (isset($data['type']) && !in_array($data['type'], ['multiple_choice', 'dropdown'])) {
                $form->remove('options');
            } elseif (isset($data['type']) && in_array($data['type'], ['multiple_choice', 'dropdown'])) {
                $form->add('options', TextareaType::class, [
                    'label' => 'Options (comma-separated)',
                    'required' => false,
                    'mapped' => true,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}