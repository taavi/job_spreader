<?
class Spreader2 {
	function __construct($blockpool, $spread) {
		reset($blockpool);
		$this->blockpool = $blockpool;
		$this->spread = min(count($blockpool), $spread);
		$this->feeders = array();
		foreach (range(1, $this->spread) as $_) {
			$this->feeders[] = $this->_next_block();
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
				$next = $this->_next_item($this->feeders[$this->current_index]);
				$found = TRUE;
			} catch (StopIteration $e) {
				try {
					$this->feeders[$this->current_index] = $this->_next_block();
				} catch (StopIteration $e) {
					array_splice($this->feeders, $this->current_index, 1);
					$this->spread--;
					if ($this->spread > 0) {
						$this->current_index %= $this->spread;
					}
					continue;
				}
				continue;
			}
			$this->current_index = ($this->current_index + 1) % $this->spread;
		} while (!$found);
		return $next;
	}
	function _next_item(&$block) {
		list($key, $value) = each($block);
		if ($key === NULL) {
			throw new StopIteration();
		}
		return $value;
	}
	function _next_block() {
		list($key, $block) = each($this->blockpool);
		if ($key === NULL) {
			throw new StopIteration();
		}
		reset($block);
		return $block;
	}
}
