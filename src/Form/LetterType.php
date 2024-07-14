<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        for ($i = 0; $i < $options['word_length']; $i++) {
      
            $builder->add('letter' . $i, TextType::class, [
                'label' => false,
            
                'attr' => [
                    'maxlength' => 1,
                    'class' => 'letter-input'
                ],
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
