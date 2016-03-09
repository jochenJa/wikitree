<?php

/**
 * Created by PhpStorm.
 * User: jochen.janssens
 * Date: 3/03/2016
 * Time: 9:20
 */
class Treeify
{

    private $tree = [];
    private $usedEntryPoints = [];

    private function scan(string $fileContents): array
    {
        $found = [];
        preg_match_all('/(?<=\[\[)(?!http)([^\]]*)/', $fileContents, $found);

        $found = (array)reset($found);

        return array_map(
            function($link) { if($link) return explode('|', $link)[0]; },
            $found
        );
    }

    private function linkToFilePath(string $link) : string
    {
        if(! $link) throw new Exception('link is not defined.');

        $filePath = strtolower($link);
        $filePath = normalize($filePath);
        $filePath = preg_replace('/[\' ]/', '_', $filePath);
        $filePath = str_replace(':', '/', $filePath);
        $filePath .= '.txt';

        return $filePath;
    }

    public function linkedFiles(string $fileContents) : array
    {
        return array_map(
            function($link) { return $this->linkToFilePath($link); },
            $this->scan($fileContents)
        );
    }

    public function add($fileName, $linkedFiles)
    {
        $this->tree[$fileName] = $linkedFiles;
    }

    public function __toString()
    {
        $treeview = '';
        foreach($this->tree as $filename => $linked)
             $treeview .= sprintf(
                 '<div>%s<ul><li>%s</li></ul></div>', $filename, implode('</li><li>', $linked));

        return $treeview;
    }

    public function flip()
    {
        $flipped = [];
        foreach($this->tree as $filename => $linked) {
            foreach($linked as $link) {
                $flipped[$link][] = $filename;
            }
        }

        $flipped = array_filter(
            $flipped,
            function($usedInFiles) { return (count($usedInFiles) > 1); }
        );

        return $flipped;
    }

    public function reduceOn($entrypoint)
    {
        $this->usedEntryPoints[$entrypoint] = true;

        return $this->categorize(array_map(
            function($path) { return [$path]; },
            $this->getPoint($entrypoint)
        ));
    }

    public function categorize($links)
    {
        $categorized = array_reduce(
            $links,
            function($paths, $path) {
                $link = reset($path);

                $subs = array_map(
                    function($sublink) use ($path) {
                        //if(! array_key_exists($sublink, $this->usedEntryPoints))
                            array_unshift($path, $sublink);

                        return $path;
                    },
                    $this->getPoint($link)
                );

                return array_merge($paths, $subs);
            },
            []
        );

        foreach($categorized as $paths) { $this->usedEntryPoints[reset($paths)] = true; }

        return $categorized;
    }

    public function getPoint($point) { return isset($this->tree[$point]) ? $this->tree[$point] : []; }
    public function get() { return $this->tree; }
}

function normalize ($string) {
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'R'=>'R', 'r'=>'r',
    );

    return strtr($string, $table);
}