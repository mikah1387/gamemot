<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class LetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
     
            for ($i = 0; $i < $options['word_length']; $i++) {
      
                $builder->add('letter' . $i, TextType::class, [
                    'label' => false,
                
                    'attr' => [
                        'maxlength' => 1,
                        'class' => 'letter',
                        'style' => 'text-transform: uppercase;'
                       
                    ],
                    'constraints' => [
                    new NotBlank([
                        'message' => 'vous devriez entrer une lettre',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Ce champ doit contenir uniquement une lettre.',
                    ])
                    ]
                ]);
            }
        

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'word_length' => 5, // Default word length
        ]);
    }
}
