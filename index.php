<?php
/**
 * Created by PhpStorm.
 * User: jochen.janssens
 * Date: 3/03/2016
 * Time: 10:32
 */
ini_set('max_execution_time', 0);
require_once('vendor/autoload.php');
require_once('Treeify.php');

const WIKI_PATH = 'd:/wiki/pages/';
const WIKI_NAME = 'dragintra';
const SHADOW_PATH = 'd:/wiki/pages/shadowcopy';

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
    ['dragintra/poolwagenbeheerder_-_care4fleets.txt', '/dragintra/contacten/', 3],
    ['dragintra/leverancierscontacten.txt', '/dragintra/contacten/', 4],
    ['dragintra/athlon_car_lease.txt', '/dragintra/contacten/', 3],
    ['dragintra/leaseplan.txt', '/dragintra/contacten/', 3],
    ['dragintra/care4fleets.txt', '/dragintra/contacten/', 3],
    ['dragintra/ald_automotive.txt', '/dragintra/contacten/', 3],
    ['dragintra/abn_amro20.txt', '/dragintra/contacten/', 2],
    ['dragintra/arval.txt', '/dragintra/contacten/', 2],
    ['dragintra/daimler.txt', '/dragintra/contacten/', 2],
    ['dragintra/alphabet.txt', '/dragintra/contacten/', 2],
    ['dragintra/belfius_lease.txt', '/dragintra/contacten/', 2],
    ['dragintra/aig20.txt', '/dragintra/contacten/', 2],
    ['dragintra/arseus20.txt', '/dragintra/contacten/', 2],
    ['dragintra/sap20.txt', '/dragintra/contacten/', 2],
    ['dragintra/atos_leverancier.txt', '/dragintra/contacten/', 2],
    ['dragintra/jdenl20.txt', '/dragintra/contacten/', 2],
    ['dragintra/wurth20.txt', '/dragintra/contacten/', 2],
    ['dragintra/arcelormittal20.txt', '/dragintra/contacten/', 2],
    ['dragintra/blogistics20.txt', '/dragintra/contacten/', 2],
    ['dragintra/bsh20.txt', '/dragintra/contacten/', 2],
    ['dragintra/elia20.txt', '/dragintra/contacten/', 2],
    ['dragintra/henkel20.txt', '/dragintra/contacten/', 2],
    ['dragintra/mastercard_leverancier.txt', '/dragintra/contacten/', 2],
    ['dragintra/novartis20.txt', '/dragintra/contacten/', 2],
    ['dragintra/luminus20.txt', '/dragintra/contacten/', 2],
    ['dragintra/total20.txt', '/dragintra/contacten/', 2],
    ['dragintra/bank_degroof20.txt', '/dragintra/contacten/', 2],
    ['dragintra/mediahuis.txt', '/dragintra/contacten/', 2],
    ['dragintra/techdata20.txt', '/dragintra/contacten/', 2],
    ['dragintra/jde_coffee2.txt', '/dragintra/contacten/', 2],
    ['dragintra/goodman20.txt', '/dragintra/contacten/', 2],
    ['dragintra/contactgegevens_per_klant.txt', '/dragintra/contacten/', 3],
    ['dragintra/klant.txt', '/dragintra/contacten/', 3],
    ['dragintra/contacten.txt', '/dragintra/contacten/', 4],

    //['dragintra/doorbelastingen.txt', '/dragintra/accounts_payable/doorbelastingen/', 1],
    //['dragintra/type_kost.txt', '/dragintra/accounts_payable/doorbelastingen/', 4],

//    ['dragintra/afleveradres_korte_termijn.txt', '/dragintra/fleetkennis/leasing/', 4],
//    ['dragintra/opvoeren_korte_termijn.txt', '/dragintra/fleetkennis/leasing/', 4],
//    ['dragintra/levering_kt_wagen.txt', '/dragintra/fleetkennis/leasing/', 4],
//    ['dragintra/manueel_opvoeren_van_schadegeval.txt', '/dragintra/fleetkennis/leasing/', 4],
//    ['dragintra/leasing.txt', '/dragintra/fleetkennis/leasing/', 4],
//    ['dragintra/schade_met_tegenpartij_zonder_schadeapp.txt', '/dragintra/fleetkennis/leasing/', 4],
//    ['dragintra/leasing.txt', '/dragintra/fleetkennis/leasing/', 4],

];

foreach ($steps as list($ep, $path, $end))
{
    $leftovers = [];
    $paths = $tree->reduceon($ep);
    $step = 1;
    while ($end > $step)
    {
        $paths = $tree->categorize($paths);
        $step++;
    }

    $l = array_reduce(
        $paths,
        function ($set, $path) use (&$leftovers)
        {
            $first = array_shift($path);
            // keep ending paths, skip unfinished.
            if(! in_array($first, ['#','LNK'])) {
                $leftovers[$first][] = array_reverse($path);
                return $set;
            }
            //$set[in_array($first, ['#','LNK']) ? array_shift($path) : $first][] = array_reverse($path);
            $set[array_shift($path)][] = array_reverse($path);

            return $set;
        },
        []
    );
//dump($l, $leftovers);
    $step = '/dragintra/';
    foreach(array_filter(explode('/', str_replace('dragintra/','', $path))) as $dir) {
        $step .= $dir.'/';
        $dir = SHADOW_PATH.$step;
        if (!is_dir($dir))
        {
            mkdir($dir);
        }
        else
        {
            $files = glob($ep . '*');
            foreach ($files as $file)
            {
                if (is_file($file))
                {
                    unlink($file);
                }
            }
        }
    }
    $epdir = SHADOW_PATH . $path;

    foreach (array_filter($l, function ($paths) { return count($paths) == 1; }) as $file => $path)
    {
        $parts = explode('/', $file);
        if (copy(WIKI_PATH . $file, $epdir . end($parts)))
        {
            $tree->removePoint($file);
        }

    }

    $after = array_filter($l, function ($paths) { return count($paths) > 1; });
    if (!count($after))
    {
        $tree->removePoint($ep);
    }
    else
    {
        dump($ep, $after);
    }
}


$paths = $tree->reduceon('dragintra/start.txt');
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

dump($l);

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


