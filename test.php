<?php
/**
 * Created by PhpStorm.
 * User: jochen.janssens
 * Date: 3/03/2016
 * Time: 10:32
 */

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

foreach($fileList as $fileName)
{
    $linked = array_filter(
        $tree->linkedFiles(file_get_contents(WIKI_PATH.$fileName)),
        function($filename) { return file_exists(WIKI_PATH.$filename); }
    );

    if(count($linked)) {
        $tree->add(
            $fileName,
            array_unique($linked)
        );
    }
}

$paths = $tree->reduceon('S');
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
//$paths = $tree->categorize($paths);

pathify($paths);

function pathify($paths) {
   foreach($paths as $path) {
       echo sprintf(
           '<div>%s</div>',
           implode('||', array_reverse(
               array_map(
                   function($file) { return cleanupfn($file); },
                   $path
               )
           ))
       );
   }
}

function cleanupfn($filename) { return str_replace('dragintra/', '', str_replace('.txt', '', $filename));}

exit;

