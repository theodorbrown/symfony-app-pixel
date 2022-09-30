<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Support;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class GameType extends AbstractType {

    public function __construct(private Security $security) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //ajout des champs formulaire
        $builder
            ->add('title', null, [
                'label' => 'game.title'
            ])
            ->add('content', WysiwygType::class, [
                'help' => 'game.content_help',
                'label' => 'game.content',
                'attr' => [
                    'rows' => 5
                ]
                ]);

        if($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('enabled', ChoiceType::class, [
                'label' => 'game.enabled',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'expanded' => true
            ]);
        }

        $builder->add('publishedAt', null, [
                'label' => 'Date de publication',
                'date_widget' => 'single_text'
            ])
            ->add('support', EntityType::class, [
                'class' => Support::class,
                'required'=> false,
                //gouper par constructeur
                'group_by' => 'constructor',
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.year');
                }
            ])

            //Ajout du formulaire
            ->add('image', ImageType::class)

            ->add('deleteImage', CheckboxType::class, [
                'label' => 'game.delete_image',
                'required' => false
            ])

            //ajouter un champ
            ->add('categories', EntityType::class, [
                'label' => 'game.categories',
                'multiple' => true,
                'expanded' => true,
                'class' => Category::class
            ])
        ;
    }

    //indique que le formulaire est conçu pour entity Game, peut bug avec des formulaires imbriqués.
    public function configureOptions(OptionsResolver $resolver)
    {
        //indique que ce formulaire est lié à l'entité Game
        $resolver->setDefaults([
            'data_class' => Game ::class //retourne une chaine avec l'espace de nom de cette classe
        ]);
    }
}