<?php

namespace App\Helpers;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailHelper
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function sendEmail(User $user, string $subject, string $template, array $parameters = []): bool
    {
        if (isset($parameters['from'])) {
            $from = new Address($parameters['from'][0], $parameters['from'][1]);
            unset($parameters['from']);
        } else {
            $from = new Address('webmaster@somda.nl', 'Somda');
        }

        $message = (new TemplatedEmail())
            ->from($from)
            ->to(new Address($user->email, $user->username))
            ->subject($subject)
            ->htmlTemplate('emails/'.$template.'.html.twig')
            ->textTemplate('emails/'.$template.'.text.twig')
            ->context($parameters);
        try {
            $this->mailer->send($message);
            return true;
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical(
                'Failed to send email with subject "'.$subject.'" to user with id '.$user->id
            );
            $this->logger->critical($exception->getMessage());
        }
        return false;
    }
}
