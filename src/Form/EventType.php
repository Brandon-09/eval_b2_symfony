<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Le titre est obligatoire.'
                    ),
                ],
            ])

            ->add('date', DateTimeType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'input' => 'datetime',
                'constraints' => [
                    new Assert\NotNull(
                        message: 'La date est obligatoire.'
                    ),
                ],
            ])

            ->add('information', TextareaType::class, [
                'label' => 'Informations',
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Les informations sont obligatoires.'
                    ),
                ],
            ])

            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Concert' => 'concert',
                    'Conférence' => 'conference',
                    'Atelier' => 'atelier',
                    'Sport' => 'sport',
                ],
            ])

            ->add('maxPlaces', IntegerType::class, [
                'label' => 'Nombre de places',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Positive(
                        message: 'Le nombre de places doit être supérieur à 0.'
                    ),
                ],
            ])

            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\Image(
                        maxSize: '5M',
                        mimeTypesMessage: 'Veuillez choisir une image valide.'
                    ),
                ],
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
