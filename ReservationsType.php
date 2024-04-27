<?php

namespace App\Form;

use App\Entity\Oeuvres;
use App\Entity\Reclamation;
use App\Entity\Reservations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationsType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('dateReservation')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'Pending',
                    'Confirmed' => 'Confirmed',
                    'Cancelled' => 'Cancelled'
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('oeuvreID', HiddenType::class, [
                'mapped' => false,
                'data' => $options['oeuvreID'] ?? null  // Ensure the oeuvre ID is passed as an option when creating the form
            ]);
        $entityManager = $this->entityManager;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($entityManager) {
            $data = $event->getData();
            if (!empty($data['oeuvreID'])) {
                $reclamation = $entityManager->getRepository(Oeuvres::class)->find($data['oeuvreID']);
                $data['oeuvreID'] = $reclamation;
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservations::class,
            'oeuvreID' => null
        ]);
    }
}
