<?php

namespace App\Form;

use App\Entity\Tickets;
use App\Entity\TicketStatuses;
use App\Entity\TicketTypes;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('gorgiasTicketId')
            ->add('subject')
            ->add('priority')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('userId', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'id',
            ])
            ->add('status', EntityType::class, [
                'class' => TicketStatuses::class,
                'choice_label' => 'id',
            ])
            ->add('type', EntityType::class, [
                'class' => TicketTypes::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
