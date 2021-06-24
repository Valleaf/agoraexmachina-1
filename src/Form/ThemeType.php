<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Theme;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use \Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use \Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use \Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Validator\Constraints\File;

class ThemeType extends AbstractType
{
    /**
     * Pour créer un thème, doivent être renseignés :
     * - Le nom du thème
     * - La description du thème
     * - Une image (facultatif)
     * - La visibilité du thème (Public ou non?)
     * - L'autorisation de la délégation ou non
     * - La profondeur de la délégation (Facultatif)
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('name')
            ->add('description', CKEditorType::class, [
                'config' => [
                    'uiColor' => '#ffffff',
                    'toolbar' => 'full',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'help'=>'img.max.size.2048',
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => 'Public?',
                'required' => false
            ])
            ->add('voteType', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'choices' => [
                    'votes.with.delegation' => [
                        'vote-three-levels' => 'yes-delegation',
                    ],
                    'votes.without.delegations' => [
                        'vote-three-levels' => 'no-delegation',
                        'vote-weighted' => 'weighted',
                        'vote-five-levels' => 'levelled',
                    ],
                ],
            ])
            ->add('delegationDeepness', NumberType::class, [
                'disabled' => false,
                'required' => false,
            ]);

        # On affiche ou non le champ delegationsDeepness si il est nécessaire
        # Premiere etape, ajouter au formulaire le champ si voteType est egal a yes-delegation(Equivalent a un
        # systeme de votes a 3 niveaux avec delegation)
        $formModifier = function (FormInterface $form, string $voteType = 'yes-delegation') {
            if ($voteType === 'yes-delegation') {
                $form
                    ->add('delegationDeepness', NumberType::class, [
                        'attr' => ['min' => '0'],
                        'required' => false
                    ]);
            }
        };
        # Seconde étape, on ajoute un EventListener qui va recuperer le formulaire
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                # $data représente une entité Theme
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getVoteType());
            }
        );

        # On ajoute un event listener sur le post submit
        $builder->get('voteType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                # On récupère le theme dans le post submit, pour recuperer toutes les informations y compris l'ID
                $theme = $event->getForm()->getData();

                # On ajoute au parent les fonctions en callback
                $formModifier($event->getForm()->getParent(), $theme);
            }
        );

        $builder->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Theme::class,
        ]);
    }

}