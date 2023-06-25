<?php
declare(strict_types=1);

namespace App\Form;

use App\Generics\ConstraintGenerics;
use App\Generics\FormGenerics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;

class UserLostPassword extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                FormGenerics::KEY_ATTRIBUTES => [FormGenerics::KEY_ATTRIBUTES_MAX_LENGTH => 60],
                FormGenerics::KEY_CONSTRAINTS => [
                    new Email([ConstraintGenerics::MESSAGE => 'Dit is geen geldig e-mailadres']),
                ],
                FormGenerics::KEY_LABEL => 'Geef je e-mailadres om een nieuw wachtwoord te ontvangen',
                FormGenerics::KEY_REQUIRED => true,
            ]);
    }
}
