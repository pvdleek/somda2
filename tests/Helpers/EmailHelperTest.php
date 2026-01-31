<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\User;
use App\Helpers\EmailHelper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailHelperTest extends TestCase
{
    private EmailHelper $email_helper;
    private LoggerInterface $logger;
    private MailerInterface $mailer;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->email_helper = new EmailHelper($this->logger, $this->mailer);
    }

    public function testSendEmailSuccess(): void
    {
        $user = $this->createMock(User::class);
        $user->id = 123;
        $user->email = 'test@example.com';
        $user->username = 'testuser';

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));

        $result = $this->email_helper->sendEmail($user, 'Test Subject', 'test_template');

        $this->assertTrue($result);
    }

    public function testSendEmailWithCustomFrom(): void
    {
        $user = $this->createMock(User::class);
        $user->id = 123;
        $user->email = 'test@example.com';
        $user->username = 'testuser';

        $parameters = [
            'from' => ['custom@example.com', 'Custom Sender'],
            'other_param' => 'value',
        ];

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));

        $result = $this->email_helper->sendEmail($user, 'Test Subject', 'test_template', $parameters);

        $this->assertTrue($result);
    }

    public function testSendEmailWithAdditionalParameters(): void
    {
        $user = $this->createMock(User::class);
        $user->id = 123;
        $user->email = 'test@example.com';
        $user->username = 'testuser';

        $parameters = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));

        $result = $this->email_helper->sendEmail($user, 'Test Subject', 'test_template', $parameters);

        $this->assertTrue($result);
    }
}
