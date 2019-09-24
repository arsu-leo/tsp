<?php
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);

// Script to validate output

// requires
$srcPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
require_once($srcPath . 'Util.php');

$cities = Util::readCitiesFromPath();

// Validate to be able to run
// THis doesn't work if debugging
if(!isset($GLOBALS['_SERVER']))
{
  throw new Exception('Can\'t validate output, php not found to call');
}
// THis doesnt work with debugger ON
if(!isset($GLOBALS['_SERVER']['_']))
{
  throw new Exception('Can\'t validate output, php not found to call');
}

$exeCommand = $GLOBALS['_SERVER']['_'];
$solverPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'solve.php';
$cmd        = "{$exeCommand} {$solverPath}";

$starttime  = microtime(true);
$output     = shell_exec($cmd);
$endtime    = microtime(true);
$timediff   = $endtime - $starttime;


echo $output . PHP_EOL . PHP_EOL;
$outputCities = explode(chr(10), $output);

assert(count($cities) == count($outputCities), 'Ammount of cities matches output splitted cities');

for($i = 0; $i < count($cities); $i++)
{
  $city = $cities[$i]['city'];
  assert(in_array($city, $outputCities), "Output validation, city {$city} not in output");
}

echo PHP_EOL . PHP_EOL;
$sec = number_format($timediff / 1000, 3);
echo "Time taken: {$sec} seconds" . PHP_EOL;

$maxMinutes = 15;
assert($sec < $maxMinutes * 60, "Time took les than {$maxMinutes} minutes");

echo "Test ok" . PHP_EOL;
