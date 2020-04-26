<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\RequestStack;

class FlashHelper
{
    public const FLASH_TYPE_INFORMATION = 'info';
    public const FLASH_TYPE_WARNING = 'warn';
    public const FLASH_TYPE_ERROR = 'alert';

    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function add(string $type, string $message): void
    {
        if (!in_array($type, [self::FLASH_TYPE_INFORMATION, self::FLASH_TYPE_WARNING, self::FLASH_TYPE_ERROR])) {
            $type = self::FLASH_TYPE_ERROR;
        }
        $this->requestStack->getCurrentRequest()->getSession()->getFlashBag()->add($type, $message);
    }
}
