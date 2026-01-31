<?php
declare(strict_types=1);

namespace App\Tests\Helpers;

use App\Entity\ForumDiscussion;
use App\Entity\ForumPost;
use App\Entity\User;
use App\Helpers\FlashHelper;
use App\Helpers\FormHelper;
use App\Helpers\RedirectHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FormHelperTest extends TestCase
{
    private FormHelper $form_helper;
    private ManagerRegistry $doctrine;
    private FormFactoryInterface $factory;
    private FlashHelper $flash_helper;
    private RedirectHelper $redirect_helper;
    private EntityManagerInterface $entity_manager;

    protected function setUp(): void
    {
        $this->doctrine = $this->createMock(ManagerRegistry::class);
        $this->factory = $this->createMock(FormFactoryInterface::class);
        $this->flash_helper = $this->createMock(FlashHelper::class);
        $this->redirect_helper = $this->createMock(RedirectHelper::class);
        $this->entity_manager = $this->createMock(EntityManagerInterface::class);

        $this->form_helper = new FormHelper(
            $this->doctrine,
            $this->factory,
            $this->flash_helper,
            $this->redirect_helper
        );
    }

    public function testGetDoctrine(): void
    {
        $result = $this->form_helper->getDoctrine();

        $this->assertSame($this->doctrine, $result);
    }

    public function testGetFactory(): void
    {
        $result = $this->form_helper->getFactory();

        $this->assertSame($this->factory, $result);
    }

    public function testGetFlashHelper(): void
    {
        $result = $this->form_helper->getFlashHelper();

        $this->assertSame($this->flash_helper, $result);
    }

    public function testGetRedirectHelper(): void
    {
        $result = $this->form_helper->getRedirectHelper();

        $this->assertSame($this->redirect_helper, $result);
    }

    public function testFinishFormHandlingWithFlashMessage(): void
    {
        $this->doctrine
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($this->entity_manager);

        $this->entity_manager
            ->expects($this->once())
            ->method('flush');

        $this->flash_helper
            ->expects($this->once())
            ->method('add')
            ->with(FlashHelper::FLASH_TYPE_INFORMATION, 'Success!');

        $redirect_response = $this->createMock(RedirectResponse::class);
        $this->redirect_helper
            ->expects($this->once())
            ->method('redirectToRoute')
            ->with('home', ['id' => 123])
            ->willReturn($redirect_response);

        $result = $this->form_helper->finishFormHandling('Success!', 'home', ['id' => 123]);

        $this->assertSame($redirect_response, $result);
    }

    public function testFinishFormHandlingWithoutFlashMessage(): void
    {
        $this->doctrine
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($this->entity_manager);

        $this->entity_manager
            ->expects($this->once())
            ->method('flush');

        $this->flash_helper
            ->expects($this->never())
            ->method('add');

        $redirect_response = $this->createMock(RedirectResponse::class);
        $this->redirect_helper
            ->expects($this->once())
            ->method('redirectToRoute')
            ->with('home', [])
            ->willReturn($redirect_response);

        $result = $this->form_helper->finishFormHandling('', 'home');

        $this->assertSame($redirect_response, $result);
    }

    public function testAddPost(): void
    {
        $user = $this->createMock(User::class);
        $discussion = $this->createMock(ForumDiscussion::class);

        $this->doctrine
            ->expects($this->exactly(3))
            ->method('getManager')
            ->willReturn($this->entity_manager);

        $this->entity_manager
            ->expects($this->exactly(3))
            ->method('persist');

        $discussion
            ->expects($this->once())
            ->method('addPost')
            ->with($this->isInstanceOf(ForumPost::class));

        $result = $this->form_helper->addPost($discussion, $user, true, 'Test post content');

        $this->assertInstanceOf(ForumPost::class, $result);
        $this->assertSame($user, $result->author);
        $this->assertSame($discussion, $result->discussion);
        $this->assertTrue($result->signature_on);
        $this->assertInstanceOf(\DateTime::class, $result->timestamp);
    }

    public function testAddPostWithoutSignature(): void
    {
        $user = $this->createMock(User::class);
        $discussion = $this->createMock(ForumDiscussion::class);

        $this->doctrine
            ->expects($this->exactly(3))
            ->method('getManager')
            ->willReturn($this->entity_manager);

        $this->entity_manager
            ->expects($this->exactly(3))
            ->method('persist');

        $discussion
            ->expects($this->once())
            ->method('addPost');

        $result = $this->form_helper->addPost($discussion, $user, false, 'Test post');

        $this->assertFalse($result->signature_on);
    }
}
