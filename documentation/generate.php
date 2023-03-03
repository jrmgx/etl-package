<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Jrmgx\Etl\EtlComponentInterface;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Finder\Finder;

const TOC_START = '<!-- TOC % starts -->';
const TOC_END = '<!-- TOC % ends -->';
const MARKER_START = '<!-- config starts -->';
const MARKER_END = '<!-- config ends -->';
const CONFIG = MARKER_START . "\n%DATA%\n" . MARKER_END;
const TEMPLATE = "# %TITLE%\n\n" . CONFIG . "\n\n";

/**
 * @return class-string
 */
function resolveClass(string $path): string
{
    $cleanPath = preg_replace('`^\.\./src/`', '', $path);
    /** @var string $cleanPath */
    $cleanPath = preg_replace('`\.php$`', '', $cleanPath);
    /** @var string $className */
    $className = 'Jrmgx\Etl\\' . str_replace('/', '\\', $cleanPath);

    return $className;
}

/**
 * @param class-string $className
 *
 * @return array<string>
 */
function toNames(string $className): array
{
    $regex = preg_quote('Jrmgx\Etl\\');
    $className = preg_replace("`^$regex`", '', $className);
    /** @var string[] $parts */
    $parts = explode('\\', $className);
    $regex = '`(Pull|Read|Extract|Push|Write|Load|Transform|Filter|Mapping)$`';
    $parts[count($parts) - 1] = preg_replace($regex, '', $parts[count($parts) - 1]);

    return $parts;
}

function update(string $content, string $data, string $markerStart, string $markerEnd): string
{
    $lines = [];
    $started = false;
    foreach (explode("\n", $content) as $line) {
        if ($markerStart === trim($line)) {
            $lines[] = $data;
            $started = true;
        } elseif ($started) {
            if ($markerEnd === trim($line)) {
                $started = false;
            }
        } else {
            $lines[] = $line;
        }
    }

    return implode("\n", $lines);
}

chdir(__DIR__);

$dumper = new YamlReferenceDumper();

$finder = (new Finder())
    ->in(['../src/Extract', '../src/Load', '../src/Transform'])
    ->name('*.php')->notName('*Interface.php');

$toc = [];
foreach ($finder->files() as $file) {
    $className = resolveClass($file);
    /** @var $className EtlComponentInterface */
    $optionsDefinition = $className::optionsDefinition();

    $names = toNames($className);
    $fileName = mb_strtolower(implode('_', $names) . '.md');
    $type = array_pop($names);
    $title = implode(' ', $names) . ": $type";
    if ($optionsDefinition) {
        $data = "```yaml\n".$dumper->dumpNode($optionsDefinition->buildTree())."\n```";
    } else {
        $data = "No options for this component.";
    }
    $toc[$names[0]][$fileName] = $title;
    if (file_exists($fileName)) {
        // Update
        $data = str_replace('%DATA%', $data, CONFIG);
        $content = file_get_contents($fileName);
        $content = update($content, $data, MARKER_START, MARKER_END);
    } else {
        // Create
        $content = str_replace(['%TITLE%', '%DATA%'], [$title, $data], TEMPLATE);
    }
    file_put_contents($fileName, $content);
}

foreach ($toc as $section => $entries) {
    $start = str_replace('%', $section, TOC_START);
    $end = str_replace('%', $section, TOC_END);
    $data = $start . \PHP_EOL;
    foreach ($entries as $file => $title) {
        $data .= " - [$title](documentation/$file)\n";
    }
    $data .= $end;
    $content = file_get_contents('../README.md');
    $content = update($content, $data, $start, $end);
    file_put_contents('../README.md', $content);
}
