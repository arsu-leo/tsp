<?php
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);

// This script is to compare different param values for the best solution I found
// Is not a test

$srcPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
// requires
require_once($srcPath . 'Util.php');

$solversPath = $srcPath . 'solvers' . DIRECTORY_SEPARATOR;
require_once($solversPath . 'branch-cut-aprox' . DIRECTORY_SEPARATOR . 'v2' . DIRECTORY_SEPARATOR . 'v2.php');

// get a list of cities with positions
// Pre: first city is always Beijing
$cities = Util::readCitiesFromPath();
// ToDo: Remove path before submission
$cities = array_slice($cities, 0, 10);

assert(count($cities) > 0, "There are cities");

$distanceGraph  = Util::mapCitiesDistance($cities);
echo "Amount of cities: " . count($cities) . PHP_EOL;
function formatN(float $n)
{
  return number_format($n, 3);
}

$res    = [];
$pos    = [];
$best   = INF;
$solver = new BranchCutAproxTravelerSolverV2($distanceGraph);
for($i = 1; $i < 4; $i++)
{
  for($j = 1; $j < 7; $j++)
  {
    $pos[] = [$i, $j];
  }
}

usort($pos, function($a , $b)
{
  $minA = min($a[0], $a[1]);
  $minB = min($b[0], $b[1]);
  $r = 0;

  if($minA == $minB)
  {
    $maxA = max($a[0], $a[1]);
    $maxB = max($b[0], $b[1]);
    if($maxA == $maxB)
    {
      if($a[0] == $b[0])
      {
        return $a[1] - $b[1];
      }
      return $a[0] - $b[0];
    }
      return $maxA - $maxB;
  }
  return $minA - $minB;
});

// Params to skip
// When deep limit is > 4, then I get OOM (5GB) except when branch limit is 2
$skips = array(
  '2,2',
  '2,3', // YELDS GOOD SOLUTION 913, 7S
  '3,2',
  '2,4',
  '4,2',
  '2,5', // YELDS GREAT SOLUTION 905, 80S
  '5,2',
  '2,6', // BEST SO FAR, 881, 427S
  '6,2',
  '3,3',
  '3,4', // YELDS BETTER THAN 2,5, 902, 107S
  '4,3',
  '5,3',
  '6,3',
  '4,4',
  '5,4', // 902
  // '2,7', // OOM
  // '3,5', // OOM
  // '3,6', // OOM
  // '3,7', // OOM
  // '4,5', // OOM
  // '4,6', // OOM
);


for($i = 0; $i < count($pos); ++$i)
{
  $item             = $pos[$i];
  $branchLimit      = $item[0];
  $branchDeepLimit  = $item[1];
  $convination      = $branchLimit . ',' . $branchDeepLimit;
  echo $convination . ' ';
  if(array_search($convination, $skips) !== false)
  {
    echo "Skip" . PHP_EOL;
    continue;
  }
  $starttime        = microtime(true);
  $result           = $solver->solve($branchLimit, $branchDeepLimit);
  $endtime          = microtime(true);
  $timediff         = $endtime - $starttime;
  $result["time"]   = $timediff;
  $result["conv"]   = $convination;
  $res[]            = $result;
  if($result["distance"] < $best)
  {
    $best = $result["distance"];
  }
  echo "t: " . formatN($timediff) . ' d: ' . formatN($result["distance"]) . ' best: ' . formatN($best) . PHP_EOL;
}

for($i = 0; $i < count($res); $i++)
{
  $res[$i]["prec"] = $best / $res[$i]["distance"];
}

$byTime = $res;
usort($byTime, function($a, $b)
{
  // U sort turns return into integer, so we add some zeroes to avoid 0 values
  return $a["time"] * 10000 - $b["time"] * 10000;
});
$byPrec = $res;
usort($byPrec, function($a, $b)
{
  // U sort turns return into integer, so we add some zeroes to avoid 0 values
  return $a["prec"] * 10000 - $b["prec"] * 10000;
});

echo "by time:" . PHP_EOL;
for($i = 0; $i < count($byTime); $i++)
{
  $it = $byTime[$i];
  echo $it["conv"] . " " . formatN($it["time"]) . " " . formatN($it["prec"]) . PHP_EOL;
}

echo "by prec:" . PHP_EOL;
for($i = 0; $i < count($byPrec); $i++)
{
  $it = $byPrec[$i];
  echo $it["conv"] . " " . formatN($it["time"]) . " " . formatN($it["prec"]) . PHP_EOL;
}
