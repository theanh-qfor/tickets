<?php
class Hosts{
    var $entries=array();
    var $file;
    function __construct($file=''){
        if(!$file){
            $file='C:\Windows\System32\drivers\etc\hosts';
        }
        $this->open($file);
    }
    function open($file){
        $this->file=$file;
        $content=file_get_contents($file);
        if(!$content){
            return false;
        }
        return $this->parse($content);
    }
    function parse($content){
        $lines=explode("\n",$content);
        $lines=array_filter($lines);

        $section=array();
        $results=array();
        foreach ($lines as $line){
            if($this->isSection($line)){
                $section[]=$line;
            }else{
                if(!isset($the_section)){
                    $the_section='Fist section';
                }
                if($section) {
                    $the_section = $section;
                }
                $section=array();
                if($the_section) {
                    $key = md5(serialize($the_section));
                    $results[$key]['section'] = $the_section;
                    $results[$key]['lines'][] = $line;
                }
            }

        }
        foreach ($results as $section){
            $section_name=$this->getSectionName($section);
            $this->entries[$section_name]= $this->getItems($section);
        }

        return true;
    }
    function update($domain,$ip='',$section=''){
        if($section){
            if($ip) {
                $this->entries[$section][$domain] = $ip;
            }else{
                unset($this->entries[$section][$domain]);
            }
            return true;
        }else{
            foreach ($this->entries as $name=>&$items){
                if(isset($items[$domain])){
                    if($ip) {
                        $items[$domain] = $ip;
                    }else{
                        unset($items[$domain]);
                    }
                    return true;
                }
            }
            //we did not found section, try to adding to last section
            $last=array_pop(array_keys($this->entries));
            return $this->update($domain,$ip,$last);
        }
    }
    function removeDuplicate(){
        $duplicate=array();
        foreach ($this->entries as $section){
            foreach ($section as $domain=>$host){
                if(!isset($duplicate[$domain])){
                    $duplicate[$domain]=0;
                }else{
                    $duplicate[$domain]+=1;
                }
            }
        }
        $duplicate=array_filter($duplicate);
        foreach ($this->entries as &$section){
            foreach ($section as $domain=>$host){
                if(isset($duplicate[$domain])&&$duplicate[$domain]>0){
                    $duplicate[$domain]--;
                    unset($section[$domain]);
                }
            }
        }
    }
    function getSectionName($section){
        $section=$section['section'];
        if(is_array($section)){
            $section=implode("\n",$section);
        }
        $section=trim(preg_replace('#[^\w ]*#is','',$section));
        if(!$section){
            $section='Find me';
        }
        return $section;
    }
    function getItems($section){
        $items=array();
        $lines=$section['lines'];
        foreach ($lines as $line){
            if($item=$this->parse_line($line)){
                $host=$item['host'];
                $domains=$item['domains'];
                $comment=$item['comment'];
                foreach ($domains as $domain){
                    if($comment)$domain='#'.$domain;
                    $items[$domain]=$host;
                }
            }
        }
        return $items;
    }
    function parse_line($line){
        $words = preg_split('/\s+/', $line);
        $words = array_filter($words);
        $is_comment=false;
        if(count($words)>1){
            $host=array_shift($words);
            if(strpos($host,'#')===0){
                $is_comment=true;
                $host=str_replace('#','',$host);
            }
            if($host){
                return array('host'=>$host,'domains'=>$words,'comment'=>$is_comment);
            }
        }
        return false;
    }
    function isSection($line){
        return $line[0]=='#'&&!$this->isComment($line);
    }
    function isComment($line){
        return preg_match('/#\d.*/',$line);
    }
    function save(){
        return file_put_contents($this->file,$this->encode());
    }
    function encode(){
        $this->removeDuplicate();
        $str='';
        foreach ($this->entries as $name=> $section){
            $str.=$this->getSection($section,$name);
        }
        return $str;
    }
    function getSection($section,$name){
        $items=$section;
        $res='';
        $res.='#########################################'.PHP_EOL;
        $res.='# '.$name.PHP_EOL;
        $res.='#########################################'.PHP_EOL;
        foreach ($items as $domain=>$ip){
            if(!$ip){
                continue;
            }
            if(strpos($domain,'#')===0){
                $domain=substr($domain,1);
                $ip='#'.$ip;
            }
            $res .= "$ip $domain" . PHP_EOL;
        }
        $res.=PHP_EOL;
        return $res;
    }

}
$hosts=new Hosts();
$hosts->update('lar','','vhosts');
$hosts->save();