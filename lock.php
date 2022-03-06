<?php

class lock_sem {
    public function __construct($path, $projectID = 'a') {
	$key = ftok($path, $projectID);     kwas($key !== -1, 'ftok failed - lock_sem');
	$svs = sem_get($key, 1, 0600); kwas($svs, 'bad sem_get - lock_sem'); unset($key);
	$this->svs = $svs; unset($svs);
    }
    public function   lock() { 
	kwas(sem_acquire($this->svs), 'sem_acq failed - lock_sem'); }
    public function unlock() { 
	kwas(sem_release($this->svs), 'sem_rel failed - lock_sem'); }
    public function __destruct() {
	kwas(sem_remove($this->svs), 'sem_rem failed - lock_sem');    }
}
