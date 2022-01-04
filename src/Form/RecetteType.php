<?php

namespace App\Form;

use App\Entity\Recette;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Ex.: nom du plat'
                ]
            ])
            ->add('content', TextType::class, [
                'attr' => [
                    'placeholder' => 'Ex.: ingredient et recette'
                ]
            ])
            ->add('author', TextType::class, [
                'label' => 'author',
                'attr' => [
                    'placeholder' => 'Ex.: admin'
                ]
            ])
            ->add('categorie')
            
            ->add('img', FileType::class, [
                'required' => false,
                'label' => 'Image principale',
                'mapped' => false,
                'help' => 'png, jpg, jpeg ou jp2 - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une iamge au format PNG, JPG, JPEG ou JP2'
                    ])
                ]
            ])
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
