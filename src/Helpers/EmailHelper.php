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
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @param LoggerInterface $logger
     * @param MailerInterface $mailer
     */
    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $parameters
     * @return bool
     */
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
            ->htmlTemplate('emails/' . $template . '.html.twig')
            ->textTemplate('emails/' . $template . '.text.twig')
            ->context($parameters);
        try {
            $this->mailer->send($message);
            return true;
        } catch (TransportExceptionInterface $exception) {
            $this->logger->critical(
                'Failed to send email with subject "' . $subject . '" to user with id ' . $user->getId()
            );
            $this->logger->critical($exception->getMessage());
        }
        return false;
    }
}
