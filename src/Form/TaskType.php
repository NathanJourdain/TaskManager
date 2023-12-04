<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tâche',
                'required' => true,
            ])
            ->add('assignedTo', EntityType::class, [
                'label' => 'Assignée à',
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_EMPLOYEE%')
                        ->orderBy('u.email', 'ASC');
                },
                'class' => User::class,
                'choice_label' => 'email',
                'required' => false,
                'placeholder' => 'Tout le monde',
            ])
            ->add('recurrence', ChoiceType::class, [
                'label' => 'Récurrence',
                'choices' => [
                    'Aucune' => 'none',
                    'Tous les jours' => 1,
                    'Tous les 2 jours' => 2,
                    'Tous les 3 jours' => 3,
                    'Toutes les semaines' => 7,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }

}
