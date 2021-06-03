<?php
namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserEditFormType extends AbstractType
{
    /**
     * L'administrateur peut modifier pour un utilisateur son pseudo, son email et son rôle.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
				->add('username')
				->add('email', EmailType::class)
                ->add(
                'roles', ChoiceType::class, [
                            'choices' => [
                                'Administrateur' => 'ROLE_ADMIN',
                                'Administrateur restreint' => 'ROLE_ADMIN_RESTRICTED',
                                'Modérateur' => 'ROLE_MODERATOR',
                                'Utilisateur' => 'ROLE_USER']
                        ]
                    )
                ->add('Submit', SubmitType::class)
		;

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