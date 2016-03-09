<?php

require_once('vendor/autoload.php');
require_once('Treeify.php');

$struct = [
    'A' => '[['.implode(']]|[[', ['B','C','D']).']]',
    'B' => '[['.implode(']]|[[', ['E','F']).']]',
    'C' => '',
    'D' => '',
    'E' => '',
    'F' => '',
    'I' => '[[A]]',
    'H' => '',
    'S' => '[['.implode(']]|[[', ['X','Y','Z']).']]',
    'X' => '[['.implode(']]|[[', ['D']).']]',
    'Y' => '[['.implode(']]|[[', ['A']).']]',
    'Z' => '[['.implode(']]|[[', ['I','H']).']]'
];

$tree = new Treeify();

foreach($struct as $name => $content)
{
    $linked = $tree->scan($content);
    $tree->add($name, $linked);
}

echo $tree.'<hr><br>';

$paths = $tree->reduceOn('S');
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
pathify($paths);

$l = array_reduce(
    $paths,
    function($set, $path) {
        $set[array_shift($path)][] = array_reverse($path);

        return $set;
    },
    []
);


$findCommonPath = function($paths) {
    $steps = array_reduce(
        $paths,
        function($steps, $path) { return count($path) >= $steps ? $steps : count($path); },
        999
    );

    $column = 0;
    while($column < $steps && count(array_unique(array_column($paths, $column))) == 1) { $column++; }

    return array_unique(array_column($paths, --$column));

};

dump($l);

dump(array_map(
    function($paths) use ($findCommonPath) { return $findCommonPath($paths); },
    $l
));


function pathify($paths) {
   foreach($paths as $path) {
       echo sprintf(
           '<div>%s</div>',
           implode('||', array_reverse($path))
       );
   }
}


exit;

