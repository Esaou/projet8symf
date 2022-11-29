<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentDate = new \DateTime();

        $builder
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('form.title'),
            ])
            ->add('content', TextareaType::class, [
                'label' => $this->translator->trans('form.content'),
            ])
            ->add('expiredAt', DateTimeType::class, [
                'required' => false,
                'label' => $this->translator->trans('form.expiredAt'),
                'widget' => 'single_text',
                'attr' => [
                    'min' => $currentDate->format('Y-m-d H:i'),
                ],
                'row_attr' => [
                  'class' => 'expiredAtBloc'
                ],
            ])
        ;
    }
}
