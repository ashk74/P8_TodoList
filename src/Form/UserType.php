<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // We modify the form before defining the datas
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            //We retrieve the entity related to the form
            $entity = $event->getData();
            $form = $event->getForm();
            $form
                ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
                ])
                ->add('email', EmailType::class, ['label' => 'Adresse email'])
                ->add('isAdmin', ChoiceType::class, [
                    'choices'  => [
                        'Non' => false,
                        'Oui' => true,
                    ],
                    'mapped' => false,
                    'label' => 'Administrateur',
                    // The default value is set here
                    // If the choice exists we put it if not we put at false
                    'data' => in_array('ROLE_ADMIN', $entity->getRoles()) ? true : false
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
