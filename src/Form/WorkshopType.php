<?php

namespace App\Form;

use App\Entity\Keyword;
use App\Entity\Workshop;
use App\Entity\Theme;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Validator\Constraints\File;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class WorkshopType extends AbstractType
{
    /**
     * Pour créer un atelier, doivent être renseignés :
     * - Le thème parent
     * - Le nom de l'atelier
     * - La description de l'atelier
     * - Une image (facultatif)
     * - Une date de début et une date de fin de discussion
     * - Une date de début et une date de fin de vote
     * - Des mots-clés (facultatif)
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
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
            ->add('dateBegin')
            ->add('dateEnd')
            ->add('dateVoteBegin')
            ->add('dateVoteEnd')
            ->add('rightsSeeWorkshop', ChoiceType::class, ['choices' => ['Everyone' => 'everyone']])
            ->add('rightsVoteProposals', ChoiceType::class, ['choices' => ['Everyone' => 'everyone']])
            ->add('rightsWriteProposals', ChoiceType::class, ['choices' => ['Everyone' => 'everyone']])
            ->add('quorumRequired', NumberType::class, [
                'help' => 'percentage.required',
            ])
            ->add('rightsDelegation')
            ->add('keytext', TextType::class, [
                'help' => 'keyword.help',
                'label' => 'keyword',
                'required' => false,
                'attr'=>['autocomplete' => 'off',],
            ])
            ->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Workshop::class,
        ]);
    }

}