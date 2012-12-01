<?php
/**
 * Web Script managing the search history cloud.
 */

include_once 'FRIdbUtilities.php';

$user = $_REQUEST['pid']; 
$action = $_REQUEST['action'] ? $_REQUEST['action'] : 'refresh';

header('Content-Type: text/xml; charset=utf-8');

switch ($action) {
	case 'reset':
		eraseTagCloud($user);
		print '<cloud></cloud>';
		break;
	case 'refresh':
	default:
		$maxResults = $_REQUEST['max'] ? $_REQUEST['max'] : 10;
		$sizeby = $_REQUEST['sizeby'] ? $_REQUEST['sizeby'] : 'frequency';
		
		$pastQueries = collect_queries_tag($user);
    $children = count($pastQueries);
    
    
		if ($children > 0) {
			switch ($sizeby) {
				case 'recency':
					$history = array();

					$pastQueriesSingles = array_keys(array_count_values($pastQueries));
					
					$i = 0;
					while (count($history) < min($maxResults, count($pastQueriesSingles))) {
						if (!array_key_exists($pastQueries[$i], $history))
							$history[$pastQueries[$i]] = $maxResults - count($history);
							
						$i++;
					}

					$maxValue = max($history);

					print '<cloud count="' . count($history) . '" kind="' . $sizeby . '">';
						
					foreach ($history as $query => $ranking) {
						print '<tag ranking="' . $ranking . '" size="' . linearRescaleForMax($ranking, $maxValue, 1, 10) . '">'
						. $query . '</tag>';
					}
						
					print '</cloud>';
					break;
				case 'frequency':
				default:
					$cloud = array_count_values($pastQueries);
					$maxValue = max($cloud);

					if (count($cloud) > $maxResults) {
						$selectedQueries = array_rand($cloud, $maxResults);
						$oldCloud = $cloud;

						$cloud = array();

						foreach ($selectedQueries as $query) {
							$cloud[$query] = $oldCloud[$query];
						}
					}

					$cloud = shuffleArrayKeys($cloud);

					print '<cloud count="' . count($cloud) . '" kind="' . $sizeby . '">';
						
					foreach ($cloud as $query => $count) {
						print '<tag size="' . linearRescaleForMax($count, $maxValue, 1, 10) . '">'
						. $query . '</tag>';
					}
						
					print '</cloud>';

					break;
			}
		} else {
			print '<cloud count="0"/>';
		}
		
		break;
}

/**
 * Computes a linear rounded rescaling for $value so that
 * $max will correspond to $top and every other value will
 * correspond to an intermediate value according to $step.
 *
 * NB. For no value will 0 be returned.
 */
function linearRescaleForMax($value, $max, $step = 1, $top = 100) {
	$ratio = $value / $max;
	$rescaled = $ratio * $top;
	
	return $step * ceil($rescaled / $step);
}

function shuffleArrayKeys($array) {
	$arrayToReturn = array();
	$keys = array_keys($array);

	shuffle($keys);

	foreach ($keys as $key) {
		$arrayToReturn[$key] = $array[$key];
	}
	
	return $arrayToReturn;
}

?>