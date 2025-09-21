<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AccountType extends AbstractType
{
    private const STATES = [
        'Alabama' => 'AL', 'Alaska' => 'AK', 'Arizona' => 'AZ', 'Arkansas' => 'AR',
        'California' => 'CA', 'Colorado' => 'CO', 'Connecticut' => 'CT', 'Delaware' => 'DE',
        'District Of Columbia' => 'DC', 'Florida' => 'FL', 'Georgia' => 'GA', 'Hawaii' => 'HI',
        'Idaho' => 'ID', 'Illinois' => 'IL', 'Indiana' => 'IN', 'Iowa' => 'IA',
        'Kansas' => 'KS', 'Kentucky' => 'KY', 'Louisiana' => 'LA', 'Maine' => 'ME',
        'Maryland' => 'MD', 'Massachusetts' => 'MA', 'Michigan' => 'MI', 'Minnesota' => 'MN',
        'Mississippi' => 'MS', 'Missouri' => 'MO', 'Montana' => 'MT', 'Nebraska' => 'NE',
        'Nevada' => 'NV', 'New Hampshire' => 'NH', 'New Jersey' => 'NJ', 'New Mexico' => 'NM',
        'New York' => 'NY', 'North Carolina' => 'NC', 'North Dakota' => 'ND', 'Ohio' => 'OH',
        'Oklahoma' => 'OK', 'Oregon' => 'OR', 'Pennsylvania' => 'PA', 'Rhode Island' => 'RI',
        'South Carolina' => 'SC', 'South Dakota' => 'SD', 'Tennessee' => 'TN', 'Texas' => 'TX',
        'Utah' => 'UT', 'Vermont' => 'VT', 'Virginia' => 'VA', 'Washington' => 'WA',
        'West Virginia' => 'WV', 'Wisconsin' => 'WI', 'Wyoming' => 'WY',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('last_name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(['max' => 180]),
                ],
            ])
            ->add('phone', TelType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^[0-9\-\+\s\(\)]{7,20}$/']),
                ],
            ])
            ->add('address1', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 100]),
                ],
            ])
            ->add('state', ChoiceType::class, [
                'choices' => self::STATES,
                'placeholder' => 'Select a state',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice(['choices' => array_values(self::STATES)]),
                ],
            ])
            ->add('zip', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^\d{5}(-\d{4})?$/']),
                ],
            ])
            ->add('social', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex(['pattern' => '/^\d{4}$/']),
                ],
            ])
            ->add('credit_company', TextType::class, [
                'required' => false,
                'constraints' => [new Assert\Length(['max' => 100])],
            ])
            ->add('credit_company_user', TextType::class, [
                'required' => false,
                'constraints' => [new Assert\Length(['max' => 150])],
            ])
            ->add('credit_company_password', TextType::class, [
                'required' => false,
                'constraints' => [new Assert\Length(['max' => 150])],
            ])
            ->add('credit_company_code', TextType::class, [
                'required' => false,
                'constraints' => [new Assert\Length(['max' => 50])],
            ]);
    }
}