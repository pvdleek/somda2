<?php
declare(strict_types=1);

namespace App\Helpers;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class FlashHelper
{
    public const FLASH_TYPE_INFORMATION = 'info';
    public const FLASH_TYPE_WARNING = 'warn';
    public const FLASH_TYPE_ERROR = 'alert';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function add(string $type, string $message): void
    {
        if (!\in_array($type, [self::FLASH_TYPE_INFORMATION, self::FLASH_TYPE_WARNING, self::FLASH_TYPE_ERROR])) {
            $type = self::FLASH_TYPE_ERROR;
        }
        /**
         * @var Session $session
         */
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->getFlashBag()->add($type, $message);
    }
}
