<?php

/**
 * Queue extension to reverse the priority.
 * Calling super * -1 was an option aswell
 */
class ReversePriorityQueue extends SplPriorityQueue
{
	public function compare($d1, $d2)
	{
		if ($d1 === $d2)
			return 0;
		return $d1 < $d2
			? 1
			: -1;
	}
}
