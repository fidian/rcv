This is in response to a posting on the jsmn mailing list.  For fun, the RCV implementation is in PHP and JavaScript.  More languages are welcome!

The Results
===========

I suspect I am using old data or that my implementation doesn't agree with the city's preliminary results.  My software picked RYB (R.T. Rybak) and the city is proclaiming Betsy Hodges, who doesn't appear to be on my list.  It's possible the "minneapolis" data set is out of date.

You've been warned.  :-)

The Posting
===========

Hi JavaScriptMN,

It's been three days since the election, and still we do not have a certified winner for the mayoral election in Minneapolis. This delay, plus the computational nature of Ranked-Choice-Voting (RCV), has sparked a [discussion among some of us over at Open Twin Cities](https://groups.google.com/forum/#!topic/twin-cities-brigade/16puVbT38oU) about how RCV can be implemented as a simple computer program, what some advantages would be, and thoughts on why it hasn't yet. It's also led to two implementations of the RCV algorithm, one in Java and one in R.

Seeing folks create Java and R versions of RCV got me to wonder just how many implementations of RCV could be created by, say, the end of the weekend. Its a fun little project for anybody who's new to JavaScript, or looking for a project to spend an hour or so on. [Minneapolis has a page up with information on the process itself](http://vote.minneapolismn.gov/rcv/rcv-history), and from what I know of it, the algorithm boils down to:

Create a data structure that represents a ballot with a 1st, 2nd, and 3rd choice for office
Count up the number of 1st choice votes for each candidate. If a candidate has 50% + 1 votes, declare that candidate the winner.
Else, select the candidate with the lowest number of 1st choice votes, pop the 1st choice off of any ballot for which that candidate was the first choice, and remove that candidate from all other ballots.
Goto 2

So, here is a fun challenge from Open Twin Cities: be the first person to implement RCV in JavaScript. The person who replies to this thread first with a link to a Github repo with a working RCV implementation will get a shout-out on Twitter from Open Twin Cities, and a mention on OTC's site in a forthcoming post.

Cheers, and have fun,
Bill Bushey
Open Twin Cities


Minneapolis Links
=================

* [RCV History](http://vote.minneapolismn.gov/rcv/rcv-history) - Background about the selection of RCV along with data files regarding the election.
* [Mayor summary statement](http://vote.minneapolismn.gov/results/2013/2013-mayor-tabulation) - Also preliminary XLSX document for results.


Other Implementations
=====================

Other people were spurred by Minneapolis's shameful tabulation of votes using days of work and Excel.

* [cmagnuson/minneapolis-rcv](https://github.com/cmagnuson/minneapolis-rcv)
