<?php
namespace App\Form;

use App\Entity\Delegation;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DelegationThemeType extends AbstractType
{
    /**
     * Pour une délégation dans un thème, on va chercher la liste des utilisateurs. On peut ensuite choisir un parmi
     * cette liste TODO: Limiter les utilisateurs ayant accès à la catégorie
     * @param FormBuilderInterface $builder
     * @param array $options
     */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
				->add('userTo', EntityType::class, [
					'class'			 => User::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.username', 'ASC');
                    },
					'choice_label'	 => 'username'
				])
				->add('Submit', SubmitType::class)
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Delegation::class,
		]);
        #$resolver->setRequired('usersInCategory');
        #$resolver->setAllowedTypes('usersInCategory', array(User::class));
	}

}