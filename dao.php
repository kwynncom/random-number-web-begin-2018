<?php

require_once('/opt/kwynn/kwutils.php');
require_once('random_server.php');

class dao_web_random {
    
    const lfpath = '/tmp/kwynn_com_rand_seq_lock_begin_2020_1003_1';
    
    public function __construct($din = false, $test = false) {

	$this->istest = $test;
	
        $this->db  = 'random';
	$this->cli = new MongoDB\Client('mongodb://127.0.0.1/', [], ['typeMap' => ['array' => 'array','document' => 'array', 'root' => 'array']]);
	$this->col = $this->cli->selectCollection($this->db, $this->db);
	$this->ccoll = $this->cli->selectCollection($this->db, 'counters');
	$this->setCounter();
	$this->clean();
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
	
	$ip = web_random::getIP();
	$si = $this->getNextSequenceInfo();
	$dat['seq'] = $si['seq'];
	$dat['seq_since_mstime'] = $si['mstime'];
	$dat['ip']  = $ip;
	$this->col->insertOne($dat);	
    }
    
        public function getA() {
	$dat = $this->col->find([], ['sort' => ['seq' => -1]])->toArray();
	return $dat;
    }

private function clean() { $this->col->deleteMany(['dateData.tsdb' => ['$lt' => time() - 86400 * 2.5]]); }


    private function setCounter() {
	$res = $this->ccoll->findOne();
	if ($res) return;
	$this->ccoll->insertOne(['_id' => 'notseq', 'seq' => 1, 'mstime' => time() * 1000]);
    }
    
    protected function getNextSequenceInfo() {
	$ret = $this->ccoll->findOneAndUpdate([ '_id' => 'notseq' ], [ '$inc' => [ 'seq' => 1 ]]);
        return $ret;
    }
    
}

if (didCLICallMe(__FILE__)) web_rand_seq2::test();
    
   