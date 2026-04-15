<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label'    => 'Contenu *',
                'attr'     => [
                    'placeholder' => 'Partagez quelque chose…',
                    'rows'        => 5,
                ],
            ])
            ->add('image', UrlType::class, [
                'label'    => 'URL de l\'image',
                'required' => false,
                'attr'     => ['placeholder' => 'https://…'],
            ])
            ->add('location', TextType::class, [
                'label'    => 'Lieu',
                'required' => false,
                'attr'     => ['placeholder' => 'Paris, France'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Post::class]);
    }
}
