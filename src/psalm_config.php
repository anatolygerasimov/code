<?php

declare(strict_types=1);

require_once __DIR__ . '/ComposerLoader.php';
use Code\ComposerLoader;

$options = getopt('c:t:', ['config:', 'target:']);
/** @var string $target */
$target = $options['target'];

$composerLoader  = new ComposerLoader();
$psalmConfigFile = file_get_contents(__DIR__ . '/../configs/psalm_default.xml');
$psalmConfig     = new SimpleXMLElement($psalmConfigFile);

setUpPath();
setUpIgnore();
setUpConfigs();

$xmlDocument                     = new DOMDocument('1.0');
$xmlDocument->preserveWhiteSpace = false;
$xmlDocument->formatOutput       = true;
$xmlDocument->loadXML((string)$psalmConfig->asXML());

$formatted = new SimpleXMLElement($xmlDocument->saveXML());
$formatted->saveXML($target);

function setUpPath(): void
{
    global $composerLoader, $psalmConfig;
    foreach ($composerLoader->getAbsolutePaths('psalm.paths') as $path) {
        $psalmConfig->projectFiles->addChild('directory')->addAttribute('name', $path);
    }
}

function setUpIgnore(): void
{
    global $composerLoader, $psalmConfig;
    foreach ($composerLoader->getAbsolutePaths('psalm.skip') as $path) {
        $psalmConfig->projectFiles->ignoreFiles->addChild('directory')->addAttribute('name', $path);
    }
}

function setUpConfigs(): void
{
    global $composerLoader, $psalmConfig;
    $configs  = $composerLoader->get('psalm.config');

    if (empty($configs)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($configs), RecursiveIteratorIterator::SELF_FIRST);
    $psalmConfig->registerXPathNamespace('x', 'https://getpsalm.org/schema/config');

    foreach ($iterator as $k => $v) {
        if (!$iterator->callHasChildren()) {
            $path = '/';
            for ($i = $iterator->getDepth() - 3; $i >= 0; $i--) {
                $key = $iterator->getSubIterator($i)->key();

                if (!is_int($key)) {
                    if (str_contains($path, '//')) {
                        $path = str_replace('//', sprintf('//x:%s/', $key), $path);
                    } else {
                        $path .= sprintf('/x:%s', $key);
                    }
                }
            }

            /** @var RecursiveArrayIterator $object */
            $objectAttributes = $iterator->getSubIterator();
            $objectType       = $iterator->getSubIterator($iterator->getDepth() - 2)->key();
            $parent           = $psalmConfig->xpath($path)[0];

            $hasElement = false;
            /** @var SimpleXMLElement $item */
            foreach ($parent->{$objectType} as $item) {
                $attrMatch = 0;
                foreach ($objectAttributes as $name => $value) {
                    if ((string)$item[$name] === $value) {
                        $attrMatch++;
                    }
                }

                if ($attrMatch === count(iterator_to_array($objectAttributes))) {
                    $hasElement = true;
                    break;
                }
            }

            if (!$hasElement) {
                $elem = $parent->addChild($objectType);
                foreach ($objectAttributes as $name => $value) {
                    $elem->addAttribute($name, $value);
                }
            }
        }
    }
}
