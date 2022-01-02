<?php

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('roles', ChoiceType::class,[
                'choices' => [
                    'Administrateur' => "ROLE_SUPADMIN",
                    'Responsable Ticket & carnet' => "ROLE_CATI",
                    'Responsable Utilisateur' => "ROLE_BG"
                ],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Rôles',
                'label_attr' => array('class' => 'checkbox-inline')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
