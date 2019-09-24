<?php
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);

// Script to compare different solutions

$srcPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
// requires
require_once($srcPath . 'Util.php');

$solversPath = $srcPath . 'solvers' . DIRECTORY_SEPARATOR;
require_once($solversPath . 'brute-force' . DIRECTORY_SEPARATOR . 'v1.php');
require_once($solversPath . 'brute-force' . DIRECTORY_SEPARATOR . 'v2.php');
require_once($solversPath . 'brute-force' . DIRECTORY_SEPARATOR . 'v3.php');
require_once($solversPath . 'backtrack-cut' . DIRECTORY_SEPARATOR . 'v1.php');
require_once($solversPath . 'backtrack-cut' . DIRECTORY_SEPARATOR . 'v2.php');
require_once($solversPath . 'backtrack-cut' . DIRECTORY_SEPARATOR . 'v3.php');
require_once($solversPath . 'backtrack-cut' . DIRECTORY_SEPARATOR . 'v4.php');
require_once($solversPath . 'backtrack-cut' . DIRECTORY_SEPARATOR . 'v5.php');
require_once($solversPath . 'backtrack-priority' . DIRECTORY_SEPARATOR . 'v1.php');
require_once($solversPath . 'branch-bound' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'v1.php');
require_once($solversPath . 'branch-bound' . DIRECTORY_SEPARATOR . 'v2' . DIRECTORY_SEPARATOR . 'v2.php');
require_once($solversPath . 'branch-cut'   . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'v1.php');
require_once($solversPath . 'branch-cut'   . DIRECTORY_SEPARATOR . 'v2' . DIRECTORY_SEPARATOR . 'v2.php');
require_once($solversPath . 'branch-cut'   . DIRECTORY_SEPARATOR . 'v3' . DIRECTORY_SEPARATOR . 'v3.php');
require_once($solversPath . 'branch-cut-aprox' . DIRECTORY_SEPARATOR . 'v1' . DIRECTORY_SEPARATOR . 'v1.php');
require_once($solversPath . 'branch-cut-aprox' . DIRECTORY_SEPARATOR . 'v2' . DIRECTORY_SEPARATOR . 'v2.php');

// get a list of cities with positions
// Pre: first city is always Beijing
$cities = Util::readCitiesFromPath();
$cities = array_slice($cities, 0, 17);

if(!count($cities))
{
  $err = "Expected alteast 1 city";
  throw new Exception($err);
}
$distanceGraph  = Util::mapCitiesDistance($cities);
echo "Amount of cities: " . count($cities) . PHP_EOL;

function assertResult(array $result) : void
{
  assert(!!$result, "result exists");
  assert(is_array($result), "result is an array");
  assert(isset($result["route"]), "result route is set");
  assert(isset($result["distance"]), "result distance is set");
  foreach ($result["route"] as $city)
  {
    assert(is_int($city), "All route elements are integer");
  }
  assert(is_float($result["distance"]), "result distance is a float");
}

$solvers = array(
  //"brute-v1" => new BruteForceTravelerSolverV1($distanceGraph),       // works until test 12
  //"brute-v2" => new BruteForceTravelerSolverV2($distanceGraph),       // size 12 takes 2.5 minutes
  //"brute-v3" => new BruteForceTravelerSolverV3($distanceGraph),       // size 12 takes slighly under 2 minutes
  //"back-v1" => new BacktrackCutTravelerSolverV1($distanceGraph),      // size 14, 7 seconds
  //"back-v3" => new BacktrackCutTravelerSolverV3($distanceGraph),      // size 12, 3.1 seconds
  //"back-v5"   => new BacktrackCutTravelerSolverV5($distanceGraph),    // size 13, 4 sec, size 14 22 sec
  //"back-p-v1" => new BacktrackPriorityTravelerSolverV1($distanceGraph), // worse
  //"branch-v1"   => new BranchBoundTravelerSolverV1($distanceGraph),   // size 13, <4 sec, size 14 7.5 sec, 15 -> 47s
  //"branch-v2"   => new BranchBoundTravelerSolverV2($distanceGraph),   // no improvement
  //"branch-c-v1"   => new BranchCutTravelerSolverV1($distanceGraph),   // size 13, 3 sec, 14 ~ 7, 15 -> 39s, out of memory for entire file
  //"branch-c-v2"   => new BranchCutTravelerSolverV2($distanceGraph),   // no improvement
  //"branch-c-v3"   => new BranchCutTravelerSolverV3($distanceGraph),     // size 15 < 12
  //"branch-c-a-v1"   => new BranchCutAproxTravelerSolverV1($distanceGraph, 3), // size 18 > 17
  "branch-c-a-v2"   => new BranchCutAproxTravelerSolverV2($distanceGraph, 2, 5), // size 18 ~10, if break on 2, 30 ->
);
// 17 583
//

$results  = [];
$solution = false;
$it       = 2;

foreach($solvers as $name => $solver)
{
  $totalTime = 0;
  for($i = 0; $i < $it; $i++)
  {
    $starttime  = microtime(true);
    // Run!
    $result     = $solver->solve();
    $endtime    = microtime(true);
    $timediff   = $endtime - $starttime;
    $totalTime += $timediff;
  }
  $avgTime = $totalTime / $it;
  assertResult($result);
  if($solution)
  {
    assert(count($result["route"]) === count($solution["route"]), "Solution cities count match");

    // We will not verify the order as there is no exact solution
    // $pairs = [];
    // $err   = 0;
    // for($i = 0; $i < count($solution["route"]); $i++)
    // {
    //   $pairs[] = [$solution["route"][$i], $result["route"][$i]];
    //   if($solution["route"][$i] !== $result["route"][$i])
    //   {
    //     $err = 1;
    //   }
    // }
    // if($err)
    // {
    //   $errData = '';
    //   for($i = 0; $i < count($pairs); $i++)
    //   {
    //     $errData .= $pairs[$i][0] . ' ' . $pairs[$i][1] . PHP_EOL;
    //   }
    //   $errData .= "Solution cities doesn't match, dist: [0] -> " . $solution["distance"] . " vs \"{$name}\" -> " . $result["distance"];
    //   assert($err === 0, $errData);
    // }
    // We cant verify the distance as we can get different values
    //assert($solution["distance"] === $result["distance"], "Solutions doesn't match: [0] -> " . $solution["distance"] . " vs \"{$name}\" -> " . $result["distance"]);
  }
  else
  {
    assert(count($result["route"]) === count($cities), "First solution have all the cities");
    $solution = $result;
  }
  $results[] = array("name" => $name, "time" => $avgTime);
}

// Output data
$citiesBestRoute = Util::mapCitiesFromIndexes($solution["route"], $cities);
Util::writeData($citiesBestRoute);
echo PHP_EOL . "Distance: " . $solution["distance"] . PHP_EOL;
usort($results, function($a, $b)
{
  return ($b["time"] * 1000) - ($a["time"] * 1000);
});

foreach ($results as $result)
{
  echo $result['time'] . ' -> ' . $result["name"] . PHP_EOL;
}

echo implode(' ', $solution["route"]) . PHP_EOL;
