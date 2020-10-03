<?php

set_include_path('/opt/composer');
require_once('vendor/autoload.php');

function myri($max) { return random_int(1, $max); }

function myra($optin = false, $max = 100) {
    $ro = new stdClass();
    
    if ($optin === 'ht') $opt = [ 1 => 'heads', 0 => 'tails'];
    else if (is_array($optin)) $opt = $optin;
    else $opt = false;
   
    $ro->n  = myri($max);
    $ro->me = $opt ? 1 - intval(round(($ro->n - 1)/ $max)) : '';
    $ro->o  = $opt ? $opt[$ro->me] : '';
    
    if (!is_array($opt) && $optin) $ro->s = $ro->n . $optin;
    else $ro->s  = $ro->o ? $ro->o . ' (' . $ro->n . ')' : $ro->n;
    
    return $ro;
}

function mymicrotime() {
    $dateo = new DateTime();
    $r['phpdateo'] = $dateo;
    $s = $dateo->format('U.u');
    $r['utimes'] = $s;
    $r['itime'] = intval($dateo->format('U'));
    $f =  floatval($s);
    $r['utimef'] = $f;
    $r['uonly']  = floatval('0.' . $dateo->format('u'));
    $r['mstime'] = intval(round($f * 1000));
    
    return $r;
}

function getArr() {

    $r[] = myra('ht');
    $r[] = myra([0 => 'hill', 1 => 'gpg']);
    $r[] = myra(' hr', 12);
    $r[] = myra('%');
    $r[] = myra('ht');
    $t1 = myra('ht');
    $r[] = $t1;
    if ($t1->me === 1) $r[] = myra(0, 2000);
    else $r[] = myra(0, 300);
    unset($t1);
    for ($i=1; $i <= 3; $i++) $r[] = myra(0, 100);
    unset($i);
    $r['dateData'] = mymicrotime();
    
    return $r;
}

function getJSON() {
    $rarr = getArr();
    $o = new web_random();
    $o->dbPut($rarr);
    $dbarr = $o->getA();
    popIsIP($dbarr);
    
    return json_encode($dbarr);
}

function popIsIP(&$arr) {
    for ($i=0; $i < count($arr); $i++) {
	$arr[$i]['isIP'] = $arr[$i]['ip'] === web_random::getIP();
    }
}

class web_random {
        
    public function getA() {
	$dat = $this->col->find([], ['sort' => ['seq' => -1]])->toArray();
	return $dat;
    }
    
    function __construct() {
    $this->db  = 'random';
    $this->cli = new MongoDB\Client('mongodb://127.0.0.1/', [], ['typeMap' => ['array' => 'array','document' => 'array', 'root' => 'array']]);
    $this->col = $this->cli->selectCollection($this->db, $this->db);
    $this->ccoll = $this->cli->selectCollection($this->db, 'counters');
    $this->setCounter();
    $this->clean();
}

private function clean() {
    // db.getCollection('random').deleteMany({'dateData.itime' : {'$lt' : ISODate().getTime() }})
    
    $this->col->deleteMany(['dateData.itime' => ['$lt' => time() - 86400 * 2.5]]);
}

public static function getIP() {
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli';
}

public function dbPut($dat) {

    $ip = self::getIP();
    $si = $this->getNextSequenceInfo();
    $dat['seq'] = $si['seq'];
    $dat['seq_since_mstime'] = $si['mstime'];
    $dat['ip']  = $ip;
    $this->col->insertOne($dat);
}
    private function setCounter() {
	$res = $this->ccoll->findOne();
	if ($res) return;
	$this->ccoll->insertOne(['_id' => 'notseq', 'seq' => 1, 'mstime' => time() * 1000]);
    }
    
    private function getNextSequenceInfo() {
	$ret = $this->ccoll->findOneAndUpdate([ '_id' => 'notseq' ], [ '$inc' => [ 'seq' => 1 ]]);
        return $ret;
}

}