<?php

declare(strict_types=1);

namespace App\Story;

use App\Tests\Factory\GreetingFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'greeting')]
final class GreetingStory extends Story
{
    public function build(): void
    {
        GreetingFactory::createMany(10);
        // TODO build your story here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#stories)
    }

    // TODO see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#withstory-attribute //
}
