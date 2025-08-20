<?php

$ruleset = new TwigCsFixer\Ruleset\Ruleset();

$ruleset->addStandard(new TwigCsFixer\Standard\TwigCsFixer());

$config = new TwigCsFixer\Config\Config();
$config->setCacheFile(__DIR__.'/var/.php-cs-fixer.cache');
$config->setRuleset($ruleset);

return $config;
