<?php

namespace App\Form;

use App\Entity\Forum;
use App\Entity\Workshop;
use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ForumAnswerType extends AbstractType
{

    /**
     * Une réponse à un forum doit avoir un titre(nom), un sujet(description) et un forum parent (se fait en cliquant
     * sur "répondre" pour un utilisateur)
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', CKEditorType::class, [
                'config' => [
                    'uiColor' => '#ffffff',
                    'toolbar' => 'basic',
                ],
            ])->add('parentForum', EntityType::class, [
                'class' => Forum::class,
                'choice_label' => 'name',
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Forum::class,
        ]);
    }

}