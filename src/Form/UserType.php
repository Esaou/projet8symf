<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $builder->getData();

        if (false === $options['editRoles']) {
            $builder
                ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => $this->translator->trans('form.password.same'),
                    'required' => true,
                    'first_options'  => ['label' => $this->translator->trans('form.password.label')],
                    'second_options' => ['label' => $this->translator->trans('form.password.confirm')],
                ])
                ->add('email', EmailType::class, ['label' => $this->translator->trans('form.email')]);
        }
        $builder
            ->add('roles', ChoiceType::class, [
                'label' => $this->translator->trans('form.roles.label'),
                'mapped' => false,
                'placeholder' => $this->translator->trans('form.roles.placeholder'),
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'editRoles' => false,
        ]);
    }
}
