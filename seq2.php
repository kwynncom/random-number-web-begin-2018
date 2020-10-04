<?php

require_once('/opt/kwynn/kwutils.php');
require_once('random_server.php');

class web_rand_seq2 extends web_random {
    
    const lfpath = '/tmp/kwynn_com_rand_seq_lock_begin_2020_1003_1';
    
    public function __construct($din = false, $test = false) {
	parent::__construct();
	$this->istest = $test;
	// $this->put($din);
	// $this->sort();
    }
    
    public function lock() {
	
	$newf = !file_exists(self::lfpath);
	
	kwas($r = fopen(self::lfpath, 'w'), 'seq2 rand file open fail');
	kwas(flock($r, LOCK_EX),'seq2 rand file lock fail');
	if ($newf) {
	    chmod(self::lfpath, 0660);
	    kwas(chgrp(self::lfpath, "www-data"), 'chgrp failed rand seq2');
	    
	}
	if ($this->istest) {
	    sleep(4);
	    echo 'unlock' . "\n";
	}
	
	$this->lfileh = $r;
	
    }
    
    public function unlock() { kwas(flock($this->lfileh, LOCK_UN), 'unlock failed seq2 rand'); }
    
    public static function test() { $o = new self(0, 1);     }
    
    public function put($dat) {
	
	if (!$dat) return;
	
	$ip = self::getIP();
	$si = $this->getNextSequenceInfo();
	$dat['seq'] = $si['seq'];
	$dat['seq_since_mstime'] = $si['mstime'];
	$dat['ip']  = $ip;
	$this->col->insertOne($dat);	
    }
    
    private function sort() {
	
	$sq['dateData.utimei'] = 1;
	$sq['dateData.tick'  ] = 1;
	$sq['dateData.cpun'  ] = 1;
	
	
	// $this->lock();
	$res = $this->col->find(['dateData.datv' => 2], ['sort' => $sq])->toArray();
	// $this->unlock();
	
	
	
	return;
    }
    
}

if (didCLICallMe(__FILE__)) web_rand_seq2::test();
    
   