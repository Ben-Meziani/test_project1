<?php

namespace App\Form;

use App\Entity\SpaceDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpaceDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'required' => true,
            'label' => 'Name of the document',
        ])

        ->add('documents', FileType::class, [
            'required' => false,
            'multiple' => true, 
            'mapped' => false,
            'label' => 'Upload of documents',
            'constraints' => [
                new All([
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Format d\'image non adapté'
                    ])
                ])
            ],
            'attr' => [
                'accept' => '.jpg, .jpeg'
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SpaceDocument::class,
        ]);
    }
}
