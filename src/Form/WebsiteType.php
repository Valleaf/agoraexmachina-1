<?php


namespace App\Form;


use App\Entity\Website;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class WebsiteType extends AbstractType
{
    /**
     * L'administrateur peut modifier le nom, la version, l'auteur et l'email administrateur de l'application.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('version', TextType::class)
            ->add('title', TextType::class)
            ->add('email', EmailType::class)
            ->add('loginMessage', CKEditorType::class, [
                'config' => [
                    'toolbar' => 'full',
                ],
            ])
            ->add('registrationMessage', CKEditorType::class, [
                'config' => [
                    'toolbar' => 'full',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'help'=>'img.max.size.1024',
            ])
            ->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Website::class
        ]);
    }

}