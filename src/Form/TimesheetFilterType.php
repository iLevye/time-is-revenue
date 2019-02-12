<?php

namespace App\Form;

use App\Entity\Client;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimesheetFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class)
            ->add('endDate', DateType::class)
            ->add('uninvoicedRows', CheckboxType::class, [
                'label'    => 'Return only uninvoiced tasks',
                'required' => false,
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choices' => $options['clients']
            ])
            ->add('filter', SubmitType::class, ['label' => 'Filter'])
            ->add('saveInvoice', SubmitType::class, ['label' => 'Create Invoice'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'clients' => null
        ]);
    }
}
