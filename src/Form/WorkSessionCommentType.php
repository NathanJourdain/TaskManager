<?php

namespace App\Form;

use App\Entity\WorkSessionComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkSessionCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Commentaire...',
                    'class' => 'border-2 border-gray-300 rounded-md p-2 w-96',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkSessionComment::class,
        ]);
    }
}
