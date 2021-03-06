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

$dirs = [
    "/dragintra/",
    "/dragintra/algemeen/",
    '/dragintra/brol/',
    '/dragintra/accountmanager/',
    '/dragintra/fleetkennis/',
    '/dragintra/senior_driverdesk/',
    '/dragintra/junior_driverdesk/',
    '/dragintra/accounts_payable/',
    '/dragintra/orderdesk/'
];

foreach($dirs as $dir) cleanupDir($dir);

$tree = new Treeify();

foreach($fileList as $fileName)
{
    $linked = array_filter(
        $tree->linkedFiles(file_get_contents(WIKI_PATH.$fileName), WIKI_PATH.$fileName),
        function($filename) { return file_exists(WIKI_PATH.$filename); }
    );

    $tree->add(
        $fileName,
        array_unique($linked)
    );
}
$hlp = [
    $tree->removePoint('dragintra/implementatie.txt'),
    $tree->removePoint('dragintra/contactgegevens_per_klant.txt'),
    $tree->removePoint('dragintra/type_kost.txt'),
    $tree->removePoint('dragintra/datacheck_fleet_pack.txt'),
    $tree->removePoint('dragintra/nieuwe_medewerker_in_dienst.txt'),
    $tree->removePoint('dragintra/poolwagenbeheer.txt'),
    $tree->removePoint('dragintra/klantafspraken.txt'),
];
$steps = [
    ['dragintra/keydealers.txt', '/dragintra/algemeen/', 4],
    ['dragintra/klant.txt', '/dragintra/algemeen/', 6],
    ['dragintra/bedrijfsgegevens_per_klant.txt', '/dragintra/algemeen/', 4],
    ['dragintra/leverancierscontacten.txt', '/dragintra/algemeen/', 10],
    ['dragintra/invoerders.txt', '/dragintra/algemeen/', 4],
    ['dragintra/touring.txt', '/dragintra/algemeen/', 4],
    ['dragintra/jdenl20.txt', '/dragintra/algemeen/', 4],
    ['dragintra/contacten.txt', '/dragintra/algemeen/', 6],
    ['dragintra/fleet_pack_databases.txt', '/dragintra/algemeen/', 6],

    ['dragintra/trekhaak.txt', '/dragintra/senior_driverdesk/', 3],

    ['dragintra/co_regels_sap.txt', '/dragintra/orderdesk/', 3],
    ['dragintra/trekhaak_henkel.txt', '/dragintra/orderdesk/', 3],
    ['dragintra/modellen_printscreens.txt', '/dragintra/orderdesk/', 4],

    ['dragintra/afhouding_van_het_loon.txt', '/dragintra/accounts_payable/', 4],
    ['dragintra/doorbelastingen.txt', '/dragintra/accounts_payable/', 4],

    ['dragintra/blogistics60.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/standaardgegevens.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/manueel_toevoegen_van_een_bestuurder.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/bestuurders.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/upload_van_boetelijsten.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/zie_werkwijze.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/manueel_opvoeren_van_boete.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/boetelijsten.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/boetes.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/ontbrekende_gegevens_tankkaart.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/verzenden_van_tankkaart.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/opvoeren_pincode.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/opvoeren.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/brandstof1.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/brandstoflijsten.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/brandstof.txt', '/dragintra/junior_driverdesk/', 10],
    ['dragintra/manueel_opvoeren_van_schadegeval.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/upload_van_schadelijst.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/schades.txt', '/dragintra/junior_driverdesk/', 4],

    ['dragintra/klantafspraken_poolwagens.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/locatie_poolwagen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/afleveradres_korte_termijn.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/wagen_is_mobiel.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/mobiel.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/wagen_is_niet_mobiel.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/opvoeren_korte_termijn.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/korte_termijn.txt', '/dragintra/senior_driverdesk/', 6],
    ['dragintra/stilstand.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/verzekering.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/expertise_incorrect.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/akkoord_herstelling_poolwagens_per_klant.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/levering_poolwagen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/voorlopige_inname_van_een_poolwagen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/definitieve_inname_van_een_poolwagen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/levering_nieuwe_wagen_bij_eoc_oude_wagen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/opvragen_kost_neoc.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/wagenwissels.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/pechverhelping.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/banden.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/technische_keuring.txt', '/dragintra/senior_driverdesk/', 3],

    ['dragintra/budgetten_total.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/wagengevens_invullen.txt', '/dragintra/senior_driverdesk/', 6],

    ['dragintra/verlies_van_boorddocumenten.txt', '/dragintra/senior_driverdesk/', 6],
    ['dragintra/kleine_herstellingen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/glasbraak.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/diefstal_nummerplaat.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/attest_schadevrij_rijden.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/bewonerskaart.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/contractverlenging.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/onderhoud.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/overname_wagen.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/tolkosten.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/totaal_verlies.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/faq_carpolicy.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/bestickering.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/diefstal_voertuig.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/langduring_afwezigheid.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/verlies_van_tankkaart.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/verlies_sleutel.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/verzekeringsdocument_groene_kaart.txt', '/dragintra/senior_driverdesk/', 3],

    ['dragintra/herrekeningen.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/schedule.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/cac-kaarten.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/algemene_mailing.txt', '/dragintra/junior_driverdesk/', 4],

    ['dragintra/algemene_mailing_ap.txt', '/dragintra/accounts_payable/', 4],
    ['dragintra/overzicht_facturatielijsten.txt', '/dragintra/accounts_payable/', 4],
    ['dragintra/facturatie.txt', '/dragintra/accounts_payable/', 6],

    ['dragintra/leasing.txt', '/dragintra/fleetkennis/', 4],
    ['dragintra/wagen.txt', '/dragintra/fleetkennis/', 4],

    ['dragintra/login_cc_admin.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/prijzen_koppelen_aan_reference_lijst.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/maandelijkse_wagenlijsten_opvoeren.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/dragintra_car_configurator.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/controle.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/mail_referentieofferte.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/prijsvergelijk_elia.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/uitnodiging_versturen.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/bestelproces_cc.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/bestelproces_met_vaste_wagens.txt', '/dragintra/orderdesk/', 4],
    ['dragintra/bestelproces.txt', '/dragintra/orderdesk/', 4],

    ['dragintra/conventies_invoerders.txt', '/dragintra/accountmanager/', 4],
    ['dragintra/rapportering.txt', '/dragintra/accountmanager/', 4],
    ['dragintra/escalaties.txt', '/dragintra/accountmanager/', 4],
    ['dragintra/mailings.txt', '/dragintra/accountmanager/', 4],
];
shadowcopy($steps, $tree);

foreach($hlp as list($ep, $links)) { $tree->setPoint($ep, $links); }
$steps = [
    ['dragintra/contactgegevens_per_klant.txt', '/dragintra/algemeen/', 4],
    ['dragintra/type_kost.txt', '/dragintra/accounts_payable/', 4],
    ['dragintra/klantafspraken.txt', '/dragintra/orderdesk/', 6],
    ['dragintra/poolwagenbeheer.txt', '/dragintra/senior_driverdesk/', 3],
    ['dragintra/implementatie.txt', '/dragintra/accountmanager/', 4],
    ['dragintra/datacheck_fleet_pack.txt', '/dragintra/junior_driverdesk/', 4],
    ['dragintra/nieuwe_medewerker_in_dienst.txt', '/dragintra/senior_driverdesk/', 4],
    ['dragintra/start.txt', '/dragintra/', 4],

    ['dragintra/wettekst_voordeel_alle_aard_2012.txt', '/dragintra/brol/', 4],
    ['dragintra/eerste_woord_uit_een_cel_verwijderen.txt', '/dragintra/brol/', 4],

    ['dragintra/templates_for_lease_companies.tx t', '/dragintra/brol/', 3],
    ['dragintra/dragintra_specific_wiki_s.txt', '/dragintra/brol/', 5],
    ['dragintra/bevestiging_2_klant.txt', '/dragintra/brol/', 3],
    ['dragintra/interface_usage.txt', '/dragintra/brol/', 3],
    ['dragintra/laatse_aanpassing_vaa_2012_o.a._pro-rata_berekening.txt', '/dragintra/brol/', 3],
    ['dragintra/implementation_wiki_s.txt', '/dragintra/brol/', 6],
    ['dragintra/levering_nieuwe_wagen_bij_kt_oude_wagen.txt', '/dragintra/brol/', 3],
    ['dragintra/interne_contacten_mckinsey.txt', '/dragintra/brol/', 3],
    ['dragintra/brol.txt', '/dragintra/brol/', 8],
];
shadowcopy($steps, $tree);

// update links after structuring files
$nsfiles = $tree->namespaced();
ksort($nsfiles);
dump(array_filter(array_combine(array_keys($nsfiles), array_column($nsfiles, 'namespace'))));
$nslinks = [];
foreach($nsfiles as $details) {
    if(! $details['namespace']) continue;
    foreach($details['links'] as $link) {
        $nslinks[$link] = implode(':', array_filter(explode('/', $details['namespace'])));
    }
}

//dump($nslinks);
$failedLinks = [];
foreach($fileList as $fileName)
{
    if(! array_key_exists($fileName, $nsfiles))  { dump('this one was not in the tree',$fileName); }

    $content = file_get_contents(WIKI_PATH.$fileName);
    $links = [];
    foreach($tree->scan($content) as $details) { $links[$details[0]] = $details[1]; };

//    if($fileName == 'dragintra/algemene_mailing.txt') {
//        dump($fileName, [$nsfiles[$fileName],$links, array_filter($nslinks, function($link) use($links) { return in_array($link, $links); }, ARRAY_FILTER_USE_KEY), $content]);
//    }

    foreach($links as $link) {
        if(! array_key_exists($link, $nslinks) || ! $nslinks[$link]) { $fialedLinks[] = [$link, $fileName];  continue; };

        $namespacedLink = preg_replace('/dragintra:/i', $nslinks[$link].':', $link);
        $isreplaced = 0;
        $content = str_replace('[['.$link.']]', '[['.$namespacedLink.']]', $content, $isreplaced);

        if(! $isreplaced) dump('nothing replaced, odd ...',[$link, $namespacedLink, $fileName, $content]);
        //if($link == 'Dragintra:Ubench - overzicht') dump([$nsfiles['dragintra/ubench_-_overzicht.txt'],$link, $namespacedLink, $fileName, $content]);
    }

    $parts = explode('/', $fileName);
    file_put_contents(SHADOW_PATH.$nsfiles[$fileName]['namespace'].end($parts), $content);
    //unlink(WIKI_PATH.$fileName);
}

dump('failedLinks',$failedLinks);
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

function shadowcopy($steps, Treeify $tree)
{
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
            function ($set, $path) use (&$leftovers, $ep)
            {
                $first = array_shift($path);
                // keep ending paths, skip unfinished.
                if (!in_array($first, ['#', 'LNK']))
                {
                    $leftovers[$first][] = array_reverse($path);
                    return $set;
                }
                //$set[in_array($first, ['#','LNK']) ? array_shift($path) : $first][] = array_reverse($path);
                $set[array_shift($path)][implode('|', array_map('cleanupfn', $path))] = array_reverse($path);

                return $set;
            },
            []
        );
        if(count($leftovers))dump('leftovers', $ep, $leftovers);

        $files = array_filter($l, function ($paths)
        {
            return (count($paths) == 1);
        });

        foreach ($files as $file => $xpath)
        {
            $tree->namespacePoint($file, $path);
        }

        $after = array_filter(
            $l,
            function ($paths, $file) use ($tree) { return (count($paths) > 1 && $tree->getPoint($file) != 'LNK'); },
            ARRAY_FILTER_USE_BOTH
        );

        if (!count($after)) $tree->removePoint($ep);
        else dump('point is not done.', $ep, $after);
    }
}

function cleanupDir($dir)
{
    $path = SHADOW_PATH . $dir;

    if (! is_dir($path)) mkdir($path);
    array_filter(
        glob($path . '*'),
        function($filename) { return is_file($filename) && preg_match('/.txt/', $filename) ? unlink($filename) : false; }
    );
}

