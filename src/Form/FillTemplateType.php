<?php
namespace App\Form;

use App\Entity\Template;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Positive;

class FillTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $template = $options['template'];

        foreach ($template->getQuestions() as $question) {
            $fieldName = 'question_' . $question->getId();
            $options = [
                'label' => $question->getTitle(),
            ];

            switch ($question->getType()) {
                case 'single_line':
                    $builder->add($fieldName, TextType::class, $options);
                    break;
                case 'multi_line':
                    $builder->add($fieldName, TextareaType::class, $options);
                    break;
                case 'positive_integer':
                    $builder->add($fieldName, IntegerType::class, array_merge($options, ['constraints' => [new Positive()]]));
                    break;
                case 'checkbox':
                    $builder->add($fieldName, CheckboxType::class, array_merge($options, ['required' => false]));
                    break;
                case 'multiple_choice':
                case 'dropdown':
                    
                    $optionsArray = $question->getOptions() ? explode(',', trim($question->getOptions())) : [];
                    $choices = array_combine($optionsArray, $optionsArray); 

                    $builder->add($fieldName, ChoiceType::class, array_merge($options, [
                        'choices' => $choices,
                        'expanded' => $question->getType() === 'multiple_choice', 
                        'multiple' => false, 
                        'required' => false,
                    ]));
                    break;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('template');
        $resolver->setAllowedTypes('template', Template::class);
        $resolver->setDefaults(['data_class' => null]); // Ensure no binding to Template
    }
}