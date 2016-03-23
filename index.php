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


//$tree->removePoint('dragintra/eigen_bijdrage_total.txt');
//$tree->removePoint('dragintra/trekhaak_henkel.txt');
$tree->removePoint('dragintra/implementatie.txt');
$type_kost = $tree->getPoint('dragintra/type_kost.txt');
$tree->removePoint('dragintra/type_kost.txt');
$tree->removePoint('dragintra/zie_werkwijze.txt');
$tree->removePoint('dragintra/datacheck_fleet_pack.txt');
$tree->removePoint('dragintra/nieuwe_medewerker_in_dienst.txt');

$steps = [
    ['dragintra/keydealers.txt', '/dragintra/contacten/', 4],
    ['dragintra/klant.txt', '/dragintra/contacten/', 4],
    ['dragintra/bedrijfsgegevens_per_klant.txt', '/dragintra/contacten/', 4],
    ['dragintra/leverancierscontacten.txt', '/dragintra/contacten/', 4],
    ['dragintra/invoerders.txt', '/dragintra/contacten/', 4],
    ['dragintra/touring.txt', '/dragintra/contacten/', 4],
    ['dragintra/jdenl20.txt', '/dragintra/contacten/', 4],
    ['dragintra/contactgegevens_per_klant.txt', '/dragintra/contacten/', 4],
    ['dragintra/contacten.txt', '/dragintra/contacten/', 6],

    //['dragintra/type_kost.txt', '/dragintra/accounts_payable/', 4],
    ['dragintra/doorbelastingen.txt', '/dragintra/accounts_payable/', 4],

    ['dragintra/naplaatsing_trekhaak.txt', '/dragintra/senior_driverdesk/', 3],

    ['dragintra/budget_total.txt', '/dragintra/orderdesk/', 3],
    ['dragintra/klantafspraken.txt', '/dragintra/orderdesk/', 5],

    ['dragintra/blogistics60.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/contact_hr_blogistics.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/standaardgegevens.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/manueel_toevoegen_van_een_bestuurder.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/bestuurders.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/upload_van_boetelijsten.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/boetelijsten.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/boetes.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/brandstoflijsten.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/brandstof.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/manueel_opvoeren_van_schadegeval.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/upload_van_schadelijst.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/schades.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/brandstof1.txt', '/dragintra/junior_driverdesk/', 4],

    ['dragintra/klantafspraken_poolwagens.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/locatie_poolwagen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/afleveradres_korte_termijn.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/wagen_is_mobiel.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/mobiel.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/wagen_is_niet_mobiel.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/korte_termijn.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/poolwagenbeheer.txt', '/dragintra/senior_driverdesk/', 3],
    //['dragintra/verzekering.txt', '/dragintra/senior_driverdesk/', 3],
    //['dragintra/wagenwissels.txt', '/dragintra/senior_driverdesk/', 3],

    ['dragintra/herrekeningen.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/schedule.txt', '/dragintra/junior_driverdesk/', 4],

    //['dragintra/facturatie.txt', '/dragintra/accounts_payable/', 4],

    //['dragintra/dragintra_car_configurator.txt', '/dragintra/senior_driverdesk/', 4],

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
            $set[array_shift($path)][implode('|',array_map('cleanupfn', $path))] = array_reverse($path);

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

    $files = array_filter($l, function ($paths) { return (count($paths) == 1); });
    //dump($files);
    foreach ($files as $file => $path)
    {
        $parts = explode('/', $file);
        if (copy(WIKI_PATH . $file, $epdir . end($parts)))
        {
            $tree->removePoint($file);
        }
    }

    $after = array_filter($l, function ($paths, $file) use ($tree) { return (count($paths) > 1 &&  $tree->getPoint($file) != 'LNK'); }, ARRAY_FILTER_USE_BOTH);
    if (!count($after))
    {
        $tree->removePoint($ep);
    }
    else
    {
        dump($ep, $after);
        //pathify($paths);
    }
}

$paths = $tree->reduceon('dragintra/start.txt');
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
$paths = $tree->categorize($paths);
//$paths = $tree->categorize($paths);
//
pathify($paths);

$l = array_reduce(
    $paths,
    function($set, $path) {
        $first = array_shift($path);
        $set[in_array($first, ['#',"LNK"]) ? array_shift($path) : $first][implode('|',array_map('cleanupfn', $path))] = array_reverse($path);

        return $set;
    },
    []
);

dump(array_filter($l, function ($paths, $file) use ($tree) { return $tree->getPoint($file) != 'LNK'; }, ARRAY_FILTER_USE_BOTH));

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


