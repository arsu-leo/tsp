<?php

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'Travel.php');

class TravelFactoryBCAV1
{
  public function create(array $route, int $newCity, array $distances, float $previousWeight, float $jumpWeight) : TravelBCAV1
  {
    // We dont change the distances graph if we have not traveled
    if(count($route))
    {
      $previousCity = $route[count($route) - 1];

      // Kill the distances on the origin and destination city
      for($i = 0; $i < count($distances); $i++)
      {
        $distances[$previousCity][$i] = INF;
        $distances[$i][$newCity]      = INF;
      }
    }

    // Kill Distance to city Beijing as we don't want to account for it
    $distances[$newCity][0] = INF;


    // Now we add the common minimum cost while reducing the distances graph
    // Distances has been modified after this call
    $calculatedReduction = $this->calculateWeightMinimizing($distances);

    // Weight of a travel is the sum of:
    // previous travel weight
    // current travel weight (previous, new)
    // calculated reducion
    $weight = $previousWeight + $jumpWeight + $calculatedReduction;


    $travels = count($route);
    $route[] = $newCity;

    return new TravelBCAV1($route, $newCity, $travels, $distances, $weight);
  }

  private function calculateWeightMinimizing(array &$distances) : float
  {
    // Get the minimums while reducing the distances
    $rowMin     = $this->minRow($distances);
    $columnMin  = $this->minColumn($distances);

    $weight     = 0;

    // cost is sum of both minimizations is the weight
    for ($i = 0; $i < count($distances); $i++)
    {
      $weight += $rowMin[$i] !== INF
        ? $rowMin[$i]
        : 0;
      $weight += $columnMin[$i] !== INF
        ? $columnMin[$i]
        : 0;
		}

		return $weight;
  }

  protected function minRow(&$distances) : array
	{
		// initialize row array to INF
		$min = array_fill(0, count($distances), INF);

		// min[i] contains minimum in row i
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] < $min[$i])
        {
					$min[$i] = $distances[$i][$j];
        }
      }
    }

		// substract on each row the minimum found for itself
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] !== INF && $min[$i] !== INF)
        {
					$distances[$i][$j] -= $min[$i];
        }
      }
    }

    return $min;
	}

	protected function minColumn(&$distances) : array
	{
		// initialize row array to INF
		$min = array_fill(0, count($distances), INF);

		// min[i] contains minimum in column i
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] < $min[$j])
        {
					$min[$j] = $distances[$i][$j];
        }
      }
    }

		// substract on each column the minimum found for itself
    for ($i = 0; $i < count($distances); $i++)
    {
      for ($j = 0; $j < count($distances); $j++)
      {
        if ($distances[$i][$j] !== INF && $min[$j] !== INF)
        {
          $distances[$i][$j] -= $min[$j];
        }
      }
    }

    return $min;
	}
}
