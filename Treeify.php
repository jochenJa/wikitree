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
    private $nonExisting = [];
    private $namespaced = [];

    public function scan(string $fileContents): array
    {
        $found = [];
        preg_match_all('/(?<=\[\[)(?!http)([^\]]*)/', $fileContents, $found);

        $found = array_filter(reset($found), function($link) { return (! preg_match('/:\|/', $link)); });
        return array_map(
            function($link) { return [explode('|', $link)[0], $link]; },
            $found
        );
    }

    private function linkToFilePath(string $link, $parent = false) : string
    {
        $data = [$link, $parent];
        if(! $link) throw new Exception('link is not defined.');

        $filePath = normalize($link);
        $data['norm'] = $filePath;
        $filePath = strtolower($filePath);
        $data['lwr'] = $filePath;
        $filePath = preg_replace('/[\' <>&()\/,:+!]+/', '_', trim($filePath));
        $filePath = preg_replace('/[.]{2,}/', '_', $filePath);
        $filePath = preg_replace('/_+/', '_', $filePath);
        $filePath = preg_replace('/^_|_$/', '', $filePath);
        $data['special'] = $filePath;
        $filePath = preg_replace('/^(dragintra_)/', 'dragintra/', $filePath);
        $data['path'] = $filePath;
        $filePath .= '.txt';
        $data['file'] = $filePath;
        if(!file_exists('d:/wiki/pages/'.$filePath)) { $this->nonExisting[$parent][$filePath] = $data; };
        return $filePath;
    }

    public function linkedFiles(string $fileContents, $parent = false) : array
    {
        return array_map(
            function($details)use ($parent) {
                $file = $this->linkToFilePath(reset($details), $parent);

                if(! isset($this->namespaced[$file])) $this->namespaced[$file] = ['links' => [], 'namespace' => null];
                $this->namespaced[$file]['links'][] =  $details[1];

                return $file;
            },
            array_filter($this->scan($fileContents), function($details) { return (reset($details)); })
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
        {
            if(file_exists(WIKI_PATH.$filename)) {
                $treeview .= sprintf(
                    '[%s] => [%s]<br>',
                    $filename,
                    is_array($linked) ? implode(',', $linked) : $linked
                );
            }
        }

        return $treeview;
    }


    public function reduceOn($entrypoint)
    {
        $paths = $this->getPoint($entrypoint);
        if(! is_array($paths)) { dump($entrypoint); return []; }

        $paths = $this->categorize(array_map(
            function($path) use ($entrypoint) { return [$path, $entrypoint]; },
            $paths
        ));

        return array_merge([['#', $entrypoint]], $paths);
    }

    public function categorize($links)
    {
        $categorized = array_reduce(
            $links,
            function($paths, $path) {
                $link = reset($path);

                $subs = [];
                if(is_array($this->getPoint($link))) {
                    $subs = array_map(
                        function($sublink) use ($path) {
                            if(in_array($sublink, $path)) { $sublink = '#'; }
                            array_unshift($path, $sublink);

                            return $path;
                        },
                        $this->getPoint($link)
                    );
                }

                if($link != '#') array_unshift($path, '#');

                return array_merge($paths, array_merge([$path], $subs));
            },
            []
        );

        return $categorized;
    }

    public function getPoint($point) { return isset($this->tree[$point]) ? $this->tree[$point] : []; }
    public function setPoint($point, $links) { return $this->tree[$point] = $links; }
    public function get() { return $this->tree; }
    public function nonExisting() { return $this->nonExisting; }

    public function removePoint($ep)
    {
        $data = [$ep, $this->getPoint($ep)];
        $this->setPoint($ep, 'LNK');

        return $data;
    }

    public function namespacePoint($file, $wikiPath)
    {
        if(! isset($this->namespaced[$file])) $this->namespaced[$file] = ['links' => [], 'namespace' => $wikiPath];
        else if($wikiPath && ! $this->namespaced[$file]['namespace']) $this->namespaced[$file]['namespace'] = $wikiPath;

        $this->setPoint($file, 'LNK');
    }

    public function namespaced() {  return $this->namespaced; }
}

function normalize ($string) {
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'Ž'=>'Z', 'ž'=>'z',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', '²'=>'&', '³'=>'&',
    );

    return strtr($string, $table);
}