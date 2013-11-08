#!/usr/bin/env php
<?php
ini_set('date.timezone', 'CST6CDT');
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

$dirname = $GLOBALS['argv'][1];

if (empty($dirname)) {
    echo "Specify directory name on command line\n";
    exit;
}

$candidates = trim(file_get_contents($dirname . '/candidates.txt'));
$candidates = explode("\n", $candidates);

$votes = trim(file_get_contents($dirname . '/votes.txt'));
$votes = explode("\n", $votes);

foreach ($votes as $k => $v) {
    $votes[$k] = explode(" ", $v);
}

$winner = australianVoting($candidates, $votes);
echo $winner . "\n";

function australianVoting($candidates, $votes) {
	if (empty($candidates) || ! is_array($candidates) || empty($votes) || ! is_array($votes)) {
		return 'no candidates';
	}

	foreach ($votes as $v) {
		if (empty($v) || ! is_array($v)) {
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
        if (count($voter) > 0) {
            $topVote = $voter[0];

            if (empty($tallies[$topVote])) {
                $tallies[$topVote] = 0;
            }

            $tallies[$topVote] ++;
        }
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

