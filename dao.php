<?php

require_once('/opt/kwynn/kwutils.php');
require_once('random_server.php');
require_once('lock.php');

class dao_web_random {
    
    public function __construct($din = false, $test = false) {

	$this->istest = $test;
	
        $this->db  = 'random';
	$this->cli = new MongoDB\Client('mongodb://127.0.0.1/', [], ['typeMap' => ['array' => 'array','document' => 'array', 'root' => 'array']]);
	$this->col = $this->cli->selectCollection($this->db, $this->db);
	$this->ccoll = $this->cli->selectCollection($this->db, 'counters');
	$this->setCounter();
	$this->clean();
	$this->locko = new lock_sem(__FILE__);
    }
    
    public function   lock() { $this->locko->  lock();   }
    public function unlock() { $this->locko->unlock();   }

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
    
   