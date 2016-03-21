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

$steps = [
    ['dragintra/poolwagenbeheerder_-_care4fleets.txt', '/dragintra/leverancierscontacten/', 3],
    //['dragintra/leverancierscontacten.txt', '/dragintra/leverancierscontacten/', 4],
    //['dragintra/athlon_car_lease.txt', '/dragintra/contacten/', 3],
    //['dragintra/leaseplan.txt', '/dragintra/contacten/', 3]
];

array_walk(
    $steps,
    function($data) use (&$tree) {
        list($ep, $path, $end) = $data;

        $paths = $tree->reduceon($ep);
        $step = 0;
        while($end > $step) {
            $paths = $tree->categorize($paths);
            $step++;
        }

        $l = array_reduce(
            $paths,
            function($set, $path) {
                $first = array_shift($path);
                $set[$first == '#' ? array_shift($path) : $first][] = array_reverse($path);

                return $set;
            },
            []
        );

        $ep = SHADOW_PATH.$path;
        if(! is_dir($ep)) mkdir($ep);
        else {
            $files = glob($ep.'*');
            foreach($files as $file){ if(is_file($file)) unlink($file); }
        }

        foreach(array_filter($l, function($paths) { if(count($paths) > 1) dump($paths);return count($paths) == 1; }) as $file => $path) {
            $parts = explode('/', $file);
            if(copy(WIKI_PATH.$file, $ep.end($parts))) {
                $tree->removePoint($file);
            }
        }

        $after = array_filter($l, function($paths) { return count($paths) > 1; });
        dump($l, $after);
        if(count($after) == 0){ $tree->removePoint($ep); }
        else { dump($after); }
    }
);

dump('t',$tree->getPoint('dragintra/poolwagenbeheerder_-_care4fleets.txt'));
$paths = $tree->reduceon('dragintra/athlon_car_lease.txt');
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);

pathify($paths);

$l = array_reduce(
    $paths,
    function($set, $path) {
        $first = array_shift($path);
        $set[$first == '#' ? array_shift($path) : $first][] = array_reverse($path);

        return $set;
    },
    []
);


//$ep = SHADOW_PATH.'/dragintra/leverancierscontacten/';
//mkdir($ep);
//foreach(array_filter($l, function($paths) { return count($paths) == 1; }) as $file => $path) {
//    if(copy(WIKI_PATH.$file, $ep.$file)) {
//        $tree->removePoint($file);
//    }
//}
//
//dump(array_filter($l, function($paths) { return count($paths) != 1; }));


//echo $tree;
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


