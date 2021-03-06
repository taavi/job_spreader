<?
class Spreader1 {
	function __construct($blockpool, $spread) {
		$this->spread = $spread;
		$this->feeders = array();
		foreach (range(1,$this->spread) as $_) {
			$this->feeders[] = new Feeder1($blockpool);
		}
		$this->current_index = 0;
	}
	function next() {
		$found = FALSE;
		do {
			if ($this->spread < 1) {
				throw new StopIteration();
			}
			try {
				$next = $this->feeders[$this->current_index]->next();
				$found = TRUE;
			} catch (StopIteration $e) {
				array_splice($this->feeders, $this->current_index, 1);
				$this->spread--;
				if ($this->spread > 0) {
					$this->current_index %= $this->spread;
				}
				continue;
			}
			$this->current_index = ($this->current_index + 1) % $this->spread;
		} while (!$found);
		return $next;
	}
}

class Feeder1 {
	function __construct($blockpool) {
		$this->blockpool = $blockpool;
		$this->block = NULL;
	}
	function next() {
		$found = FALSE;
		if ($this->block === NULL) {
			$this->block = $this->blockpool->next();
		}
		do {
			try {
				$next = $this->block->next();
				$found = TRUE;
			} catch (StopIteration $e) {
				$this->block = $this->blockpool->next();
			}
		} while (!$found);
		return $next;
	}
}
