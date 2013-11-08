#!/usr/bin/env node
(function () {
    'use strict';

    var candidates, dirname, fs, votes;

    function loadCandidates(dir) {
        var candidateList, data;

        /*jslint stupid:true*/
        data = fs.readFileSync(dir + '/candidates.txt');
        /*jslint stupid:false*/

        candidateList = data.toString().trim().split('\n');

        return candidateList;
    }

    function loadVotes(dir) {
        var data, voteList;

        /*jslint stupid:true*/
        data = fs.readFileSync(dir + '/votes.txt');
        /*jslint stupid:false*/

        voteList = data.toString().trim().split('\n');
        voteList = voteList.map(function (line) {
            return line.split(' ');
        });

        return voteList;
    }

    function removeCandidates(voteList, lowestCandidates) {
        var result;

        result = [];
        voteList.forEach(function (oneSetOfVotes) {
            result.push(oneSetOfVotes.filter(function (singlePreference) {
                return lowestCandidates.indexOf(singlePreference) === -1;
            }));
        });
        return result;
    }

    function calcTallies(voteList) {
        var result, tallies;

        tallies = {};
        voteList.forEach(function (oneSetOfVotes) {
            var topVote;
            topVote = oneSetOfVotes[0];

            if (!topVote) {
                return;
            }

            if (!tallies[topVote]) {
                tallies[topVote] = 0;
            }

            tallies[topVote] += 1;
        });

        result = {
            highest: [],
            highestCount: 0,
            lowest: [],
            lowestCount: voteList.length
        };

        Object.keys(tallies).forEach(function (index) {
            var score;
            score = tallies[index];

            if (result.highestCount < score) {
                result.highestCount = score;
                result.highest = [index];
            } else if (result.highestCount === score) {
                result.highest.push(index);
            }

            if (result.lowestCount > score) {
                result.lowestCount = score;
                result.lowest = [index];
            } else if (result.lowestCount === score) {
                result.lowest.push(index);
            }
        });

        result.highestPct = result.highestCount / voteList.length;
        result.LowestPct = result.lowestCount / voteList.length;

        return result;
    }

    function winner(candidates, highest) {
        var out;

        out = [];
        highest.forEach(function (indexPlusOne) {
            if (candidates[indexPlusOne - 1]) {
                out.push(candidates[indexPlusOne - 1]);
            }
        });

        if (out.length) {
            return out.join(' + ');
        }

        return 'no winner';
    }

    function findWinner(candidateList, voteList) {
        var tallies;

        while (true) {
            tallies = calcTallies(voteList);

            if (tallies.highestPct >= 0.5) {
                return winner(candidateList, tallies.highest);
            }

            if (tallies.lowestPct === tallies.highestPct) {
                return winner(candidateList, tallies.highest);
            }

            voteList = removeCandidates(voteList, tallies.lowest);
        }
    }

    dirname = process.argv[2];
    fs = require('fs');

    if (!dirname) {
        console.log('Specify directory name on command line');
    } else {
        candidates = loadCandidates(dirname);

        if (!Array.isArray(candidates) || !candidates.length) {
            console.log('no candidates');
            return;
        }

        votes = loadVotes(dirname);

        if (!Array.isArray(votes) || !votes.length) {
            console.log('no votes');
            return;
        }

        console.log(findWinner(candidates, votes));
    }
}());
