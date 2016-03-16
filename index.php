<?php
/**
 * Created by PhpStorm.
 * User: jochen.janssens
 * Date: 3/03/2016
 * Time: 10:32
 */
require_once('vendor/autoload.php');
require_once('Treeify.php');

const WIKI_PATH = 'k:/wiki/pages/';
const WIKI_NAME = 'dragintra';
const SHADOW_PATH = 'k:/wiki/pages/shadowcopy';

$fileList = [];
if ($handle = opendir(WIKI_PATH.WIKI_NAME.'/')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $fileList[] = WIKI_NAME.'/'.$entry;
        }
    }
    closedir($handle);
}

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


$paths = $tree->reduceon('dragintra/athlon_car_lease.txt');
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
//$paths = $tree->categorize($paths);

//pathify($paths);

$l = array_reduce(
    $paths,
    function($set, $path) {
        $first = array_shift($path);
        $set[$first == '#' ? array_shift($path) : $first][] = array_reverse($path);

        return $set;
    },
    []
);

dump($l);

exit;

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


