<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserAddFormType extends AbstractType
{

    /**
     * Pour inscrire un utilisateur, l'administrateur doit renseigner :
     * - Son prénom, 40 caractères maximum
     * - Son nom, 40 caractères maximum
     * - Son pseudo, 40 caractères maximum
     * - Son email
     * - Son mot de passe, 6 caractères minimum et 4096 maximum
     * - Son rôle : Administrateur, Administrateur Restreint, Modérateur ou Utilisateur
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('username',TextType::class,[
                'label'=>'lastname'
            ])
            ->add('firstName',TextType::class,[
                'label'=>'firstname'
            ])
            ->add('lastName')
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'length.min.10',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern'=>'/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{10,}$/',
                        'message'=>'pw.regex'
                    ])
                ],
                'label' => 'Password',
                'attr' => [
                    'placeholder' => 'Password'
                ]
            ])
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Administrateur restreint' => 'ROLE_ADMIN_RESTRICTED',
                        'Modérateur' => 'ROLE_MODERATOR',
                        'Utilisateur' => 'ROLE_USER']
                ]
            )
            ->add('Submit', SubmitType::class);

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }


}