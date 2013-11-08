<?php
ini_set('date.timezone', 'CST6CDT');
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');


$candidates = trim(file_get_contents('candidates.txt'));
$candidates = explode("\n", $candidates);

$votes = trim(file_get_contents('votes.txt'));
$votes = explode("\n", $votes);

foreach ($votes as $k => $v) {
    $votes[$k] = explode(" ", $v);
}

$winner = australianVoting($candidates, $votes);
echo var_export($winner, true) . "\n";

function australianVoting($candidates, $votes) {
	if (empty($candidates) || ! is_array($candidates) || empty($votes) || ! is_array($votes)) {
		return 'no candidates';
	}

	$candidateCount = count($candidates);

	foreach ($votes as $v) {
		if (empty($v) || ! is_array($v) || count($v) != $candidateCount) {
			return 'no votes';
		}
	}

	while (true) {
		$tallies = calcTallies($votes);
		if ($tallies['highestPct'] >= 0.5) {
			return winner($candidates, $tallies['highest']);
		}
		if ($tallies['lowestPct'] == $tallies['highestPct']) {
			return winner($candidates, $tallies['highest']);
		}
		$votes = removeCandidates($votes, $tallies['lowest']);
	}
}

function calcTallies($votes) {
	$tallies = array();
	$voteCount = 0;

	foreach ($votes as $voter) {
		if (empty($tallies[$voter[0]])) {
			$tallies[$voter[0]] = 0;
		}
		$tallies[$voter[0]] ++;
	}

	$highest = null;
	$highestIndex = array();
	$lowest = null;
	$lowestIndex = array();

	foreach ($tallies as $candidate => $score) {
		if ($highest === null || $highest < $score) {
			$highest = $score;
			$highestIndex = array($candidate);
		} elseif ($highest == $score) {
			$highestIndex[] = $candidate;
		}
		if ($lowest === null || $lowest > $score) {
			$lowest = $score;
			$lowestIndex = array($candidate);
		} elseif ($lowest == $score) {
			$lowestIndex[] = $candidate;
		}
	}

	$result = array(
		'highest' => $highestIndex,
		'highestPct' => $highest / count($votes),
		'lowest' => $lowestIndex,
		'lowestPct' => $lowest / count($votes),
	);

	return $result;
}

function removeCandidates($votes, $lowest) {
	$out = array();
	foreach ($votes as $vote) {
		$newVote = array();
		foreach ($vote as $c) {
			if (! in_array($c, $lowest)) {
				$newVote[] = $c;
			}
		}
		$out[] = $newVote;
	}
	return $out;
}

function winner($candidates, $highest) {
	$out = array();

	foreach ($highest as $index) {
		$index --;
		if (! array_key_exists($index, $candidates)) {
			return null;
		}
		$out[] = $candidates[$index];
	}

	if (count($out) == 1) {
		return $out[0];
	}

	if (! count($out)) {
		return 'no winner';
	}

	return implode(' + ', $out);
}

