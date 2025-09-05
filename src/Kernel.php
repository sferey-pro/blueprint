<?php

declare(strict_types=1);

namespace App;

use App\Kernel\Attribute\{AsCommandHandler, AsEventListener, AsQueryHandler};
use App\Kernel\Symfony\DependencyInjection\Compiler\AutoConfigureDoctrineTypesPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\{ChildDefinition, ContainerBuilder};
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AutoConfigureDoctrineTypesPass());

        $container->registerAttributeForAutoconfiguration(AsQueryHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'query.bus']);
        });

        $container->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'command.bus']);
        });

        $container->registerAttributeForAutoconfiguration(AsEventListener::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'event.bus']);
        });
    }
}
