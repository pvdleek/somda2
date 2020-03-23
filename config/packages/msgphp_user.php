<?php

use App\Entity\User;
use MsgPhp\User\User as BaseUser;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    $container->extension('msgphp_user', [
        'class_mapping' => [
            BaseUser::class => User::class,
        ],
    ]);
};
