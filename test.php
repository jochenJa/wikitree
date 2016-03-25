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
        $tree->linkedFiles(file_get_contents(WIKI_PATH.$fileName), WIKI_PATH.$fileName),
        function($filename) {
            if(! file_exists(WIKI_PATH.$filename)) { return false; }
            return true;
        }
    );

    if(count($linked)) {
        $tree->add(
            $fileName,
            array_unique($linked)
        );
    }
}
dump($tree->getPoint('dragintra/budgetten_elia.txt'));

dump($tree->nonExisting());
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
            function ($set, $path) use (&$leftovers)
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
//dump($l, $leftovers);
        $step = '/dragintra/';
        foreach (array_filter(explode('/', str_replace('dragintra/', '', $path))) as $dir)
        {
            $step .= $dir . '/';
            $dir = SHADOW_PATH . $step;
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

        $files = array_filter($l, function ($paths)
        {
            return (count($paths) == 1);
        });
        //dump($files);
        foreach ($files as $file => $path)
        {
            $parts = explode('/', $file);
            if (file_exists(WIKI_PATH . $file) && copy(WIKI_PATH . $file, $epdir . end($parts)))
            {
                unlink(WIKI_PATH . $file);
            }
            $tree->removePoint($file);
        }

        $after = array_filter($l, function ($paths, $file) use ($tree)
        {
            return (count($paths) > 1 && $tree->getPoint($file) != 'LNK');
        }, ARRAY_FILTER_USE_BOTH);
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
}

