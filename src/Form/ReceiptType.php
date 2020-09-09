<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Receipt;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('createdAt')
            ->add('startDate')
            ->add('endDate')
            ->add('totalHours')
            ->add('totalPrice')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choices' => $options['users']
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choices' => $options['clients']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Receipt::class,
            'clients' => null,
            'users' => null
        ]);
    }
}
