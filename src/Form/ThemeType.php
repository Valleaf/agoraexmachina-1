<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Theme;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use \Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Validator\Constraints\File;

class ThemeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('name')
            ->add('description', CKEditorType::class,[
                'config'=>[
                    'toolbar'=>'full',
       #           'extraPlugins' => 'markdown'
                ],
       #       'plugins'=>[
       #           'markdown'=>[
       #               'path'=>'/bundles/fosckeditor/plugins/CKEditor-Markdown/markdown/',
       #               'filename'=>'plugin.js'
       #           ]
       #        ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
            ])
            ->add('isPublic',CheckboxType::class,[
                'label'=>'Public?',
                'required'=>false
            ])
            ->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Theme::class,
        ]);
    }

}