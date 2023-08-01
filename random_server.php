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
    
    $r = nanopk(NANOPK_U | NANOPK_UNSOI | NANOPK_TSC | NANOPK_PID | NANOPK_UNS);
    $ts = $r['U'];
    $r['tsdb'] = $ts;
    $r['hronly'] = floatval('0.' . $r['Unsoi']);
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


private static function getSincePrevHu(dao_web_random $o, int $cUns) : string {
	$pa = $o->getPrev($cUns);
	$p = kwifs($pa, 'dateData', 'Uns');
	if (!$p) return '(none)';
	$d = (float)(($cUns - $p) / M_BILLION);
	return self::hu20($d);

}

private static function hu20(float $d) {
	if ($d  <  60) return sprintf('%0.1f', $d) . 's';
	$d /= 60; // min
	if ($d  <  60) return sprintf('%0.1f', $d) . ' min';
	$d /= 60; // hr
	if ($d < 24) return   sprintf('%0.1f', $d) . ' hrs';
	$d /= 24; // days
	if ($d < 8) return sprintf('%0.2f', $d) . ' days';
	return round($d) . ' days';
}

public static function testHu() {
	if (!iscli()) return;
	$a = [0.2, 0.8, 1.1, 10, 40, 200, 500, 5000, 50000, 86400, 86400 * 1.5, 86400 * 5, 86400 * 10, 86400 * 100];
	foreach($a as $x) {
		echo(self::hu20($x) . "\n");
	}
}

private static function pop() {
    $o = new dao_web_random();
    $rarr = [];
    $o->lock();
	$rarr['rand'] = self::getRandArr();
    $rarr['dateData'] = self::gethrtime();
    $o->unlock();
	$rarr['isaws'] = isAWS();
    $rarr['datv']  = 3;
	
	// given the way I'm doing this, I don't need to send in the current timestamp, but I will
	$tprhu = $rarr['prhu'] = self::getSincePrevHu($o, $rarr['dateData']['Uns']);
	$o->put($rarr);
    return $o;
}

private static function getPublic($dao) {
    
    static $myip = false;
    
    $prs = $dao->getA();
    
    if (!$myip) $myip = self::getIP();
    
    foreach($prs as $pr) {
    	$pu['rand']    = $pr['rand'];
	$pu['dhronly'] = sprintf('%0.9f',$pr['dateData']['hronly']);
	
	if (isset($pr['dateData']['coren'])) $pr['dateData']['pid'] = $pr['dateData']['coren'];
	
	$pidr = kwifs($pr, 'dateData', 'pid');
	$pid  = is_integer($pidr) ? $pidr : '?';
	$pu['pid']    = $pid;
	$pu['dtime']   = self::dtime($pr['dateData']['tsdb']);
	$pu['isIP']      = $pr['ip'] === $myip;
	$pu['seq']     = $pr['seq'];
	
	$prhu = kwifs($pr, 'prhu', ['kwiff' => '(none)']);
	$pu['prhu'] = $prhu;
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
    return json_encode($dbarr, JSON_PRETTY_PRINT);
}

public static function getIP() { return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'cli'; }


} // class

if (didCLICallMe(__FILE__)) web_random::testHu();
