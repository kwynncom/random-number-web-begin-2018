<?php

require_once('/opt/kwynn/kwutils.php');
require_once('dao.php');

class web_random {

private static function randi($max) { return random_int(1, $max); }

private static function grarr($optin = false, $max = 100) {
    $ro = new stdClass();
    
    if ($optin === 'ht') $opt = [ 1 => 'heads', 0 => 'tails'];
    else if (is_array($optin)) $opt = $optin;
    else $opt = false;
   
    $ro->n  = self::randi($max);
    $ro->me = $opt ? 1 - intval(round(($ro->n - 1)/ $max)) : '';
    $ro->o  = $opt ? $opt[$ro->me] : '';
    
    if (!is_array($opt) && $optin) $ro->s = $ro->n . $optin;
    else $ro->s  = $ro->o ? $ro->o . ' (' . $ro->n . ')' : $ro->n;
    
    return $ro;
}

private static function mymicrotime() {
    $dateo = new DateTime();
    $s = $dateo->format('U.u');
    $ts         = intval($dateo->format('U'));
    $r['tsdb'] = $ts;
    $f =  floatval($s);
    $uonlyf = floatval('0.' . $dateo->format('u'));
    $r['hronly']  = $uonlyf;
    return $r;
}

private static function getNSTime() {
    
    if (!function_exists('nanopk')) return false;
    
    $r = nanopk();
    $ts = $r['U'];
    $r['tsdb'] = $ts;
    $r['hronly'] = floatval('0.' . $r['Unsonly']);
    return $r;
}

private static function popCommonTime(&$r) {
    $r['r']     = date('r', $r['tsdb']);
}

private static function gethrtime() {
    
    $r = self::getNSTime();
    if (!$r) $r = self::mymicrotime();
    self::popCommonTime($r);
    
    return $r;
}


private static function getRandArr() {

    $r[] = self::grarr('ht');
    $r[] = self::grarr([0 => 'hill', 1 => 'gpg']);
    $r[] = self::grarr(' hr', 12);
    $r[] = self::grarr('%');
    $r[] = self::grarr('ht');
    $t1 = self::grarr('ht');
    $r[] = $t1;
    if ($t1->me === 1) $r[] = self::grarr(0, 2000);
    else $r[] = self::grarr(0, 300);
    unset($t1);
    for ($i=1; $i <= 3; $i++) $r[] = self::grarr(0, 100);
    unset($i);
    return $r;
}


private static function pop() {
    $o = new dao_web_random();
    $o->lock();
    $rarr = [];
    $rarr['rand'] = self::getRandArr();
    $rarr['dateData'] = self::gethrtime();
    $rarr['isaws'] = isAWS();
    $rarr['datv']  = 3;
    $o->put($rarr);
    $o->unlock();
    return $o;
}

private static function getPublic($dao) {
    
    static $myip = false;
    
    $prs = $dao->getA();
    
    if (!$myip) $myip = self::getIP();
    
    foreach($prs as $pr) {
    	$pu['rand']    = $pr['rand'];
	$pu['dhronly'] = sprintf('%0.9f',$pr['dateData']['hronly']);
	$pu['coren'] = $pr['dateData']['coren'];
	$pu['dtime']   = self::dtime($pr['dateData']['tsdb']);
	$pu['isIP']      = $pr['ip'] === $myip;
	$pu['seq']     = $pr['seq'];
	$pu['seq_since_mstime'] = $pr['seq_since_mstime'];
	$pus[] = $pu;
    }

    return $pus;
}

private static function dtime($ts) {
    return date('r' , $ts);
}

public static function getJSON() {
    $dao = self::pop();
    $dbarr = self::getPublic($dao);
    return json_encode($dbarr);
}

public static function getIP() { return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli'; }


}