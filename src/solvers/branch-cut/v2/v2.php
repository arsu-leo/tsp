<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ReversePriorityQueue.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'TravelFactory.php');

/**
 * Try to free memory seting up the nodes as arrays instread of objects, but that's pointless
 * Internal object pointers will possible take less space than having names arround on each array
 */

class BranchCutTravelerSolverV2
{
  public function __construct(array $distances)
  {
    $this->distances      = $this->setupInputDistance($distances);
    $this->count          = count($this->distances);
    $this->travelFactory  = new TravelFactoryBCV2();
    $this->cut            = INF;
  }

  protected function setupInputDistance(array $distances) : array
  {
    //Add city of distance 0 to everyone, one way path, let's call it middlewareCity
    for($i = 0; $i < count($distances); $i++)
    {
      // Distance to middlewareCity is 0
      $distances[$i][] = 0;
    }
    // distance from middlewareCity to anyone else is INF, there is no path to reach them
    $distances[] = array_fill(0, count($distances) + 1, INF);

    // Add city that no one can go to, let's call it finalCity
    for($i = 0; $i < count($distances) - 1; $i++)
    {
      // No one can travel to finalCity
      $distances[$i][] = INF;
    }
    // Only middlewareCity can go to finalCity with distance 0
    $distances[count($distances) - 1][] = 0;

    // Add row for finalCity, you can't go anywhere except Beijing at distance 0
    $distances[] = array_fill(0, count($distances) + 1, INF);
    $distances[count($distances) - 1][0] = 0;

    // Setup self distance is INF
    for($i = 0; $i < count($distances); $i++)
    {
      $distances[$i][$i] = INF;
    }
    return $distances;
  }

	public function solve() : array
	{

		// Queue to setup priorities of different possible branches
		$queue = new ReversePriorityQueue();

		// create initial branch and calculate it's weight
    // In this case, we start from Beijing
    $first = $this->travelFactory->create([], 0, $this->distances, 0, 0);

		// Add the first to the queue of possible results
		$queue->insert($first, $first["weight"]);

    // We always get the lower and really dont care about the others ?
		while($queue->valid())
		{
      // Get next
      $current = $queue->extract();

			// if all cities are visited
			if ($current["travels"] == $this->count - 1)
			{
        //Remove the last two cities (middlewareCity and finalCity)!
        $route = $current["route"];
        array_splice($route, count($route) - 2, 2);

        return array ('route' => $route, 'distance' => $this->calculateRouteDistance($route));
			}

      $currentCity = $current["city"];

      for ($j = 0; $j < $this->count; $j++)
			{
				if ($current["distances"][$currentCity][$j] !== INF)
				{
          // Create the next possible min path
          $next = $this->travelFactory->create($current["route"], $j, $current["distances"], $current["weight"], $current["distances"][$currentCity][$j]);

          // If we have equal or better current result than our best final result we want to keep working on that branch
          if($next["weight"] <= $this->cut)
          {
            // If this is a final result
            // Keep in mind that the last 2 jumps have 0 cost so we can disregard them
            if($next["travels"] == $this->count - 3)
            {
              //echo $this->cut . PHP_EOL;
              $this->cut = $next["weight"];
            }
            $queue->insert($next, $next["weight"]);
          }
				}
      }
    }
    // If we reach this point means there is no solution found to this problem!
    throw new Exception("Solution not found");
  }

  protected function calculateRouteDistance(array $route) : float
  {
    $total = 0;
    for($i = 0; $i < count($route) - 1; ++$i)
    {
      $city1 = $route[$i];
      $city2 = $route[$i + 1];
      $total += $this->distances[$city1][$city2];
    }
    return $total;
  }
}
