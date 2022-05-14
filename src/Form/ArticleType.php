<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Media;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('header')
            ->add('body')
            ->add('writer',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>'id'
            ])
            ->add('teaser_image',EntityType::class,[
                'class'=>Media::class,
                'choice_label'=>'id'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection'=>false,
            'data_class' => Article::class,
        ]);
    }
}
