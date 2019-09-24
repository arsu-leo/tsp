# Traveling salesman problem
Read ./doc/Zinio_PHP_Tech_Test_-_Travel_Man_.pdf

## Development roadmap
When this problem came to me, I though I could resolve it more or less without looking deep onto external resoruces. I was so wrong...

### brute-force
So, my first aproach, brute force, was just to get on the problem and familiar to the data, it was obvious that it would not work out with big numbers

### back-track
Then it just felt that backtrack would be a good initial solution. Just get a solution and compute some others and stop computing a branch if it's worse than the best found so far.

### backtrack-cut
Once I had a backtrack solution I started tring to do some code optimizations, at some point I realized this was not going to be that easy, there is no code optimization that would reduce the amount of cases, I was just wasting time, So I invested some time on investigation about this problem[1].

### branch-bound
Factorial complexity! So taking a look to different solutions proposed for this problem, I just take the branch and bound as it looked like a good one as not that complex to understand and did some extra research about it[2][3].

The video[2] was very helpful and the implementations[3] [4] I found were good enough to code a solution with some modifications to make it way more readable from my perspective and also to understand how the minimization was done. Still this problem had some differences and I struggled to find how to reflect that into the given theorical solutions until I found some resource about this special case [5].

So, after some tweaks on the input data and some modifications from the algoritm I found [5], I had then a working exact solution called "branch-bound" that was not a DFS but it was still taking quite amount of time for n = 14. Actually, on a worst case, it would do a complete DFS. Now if I waited long enough I was getting out of memory (OOM) errors.

I was able to increase the PHP memory up to **5GB** without having issues with the computer performance itself.

### branch-cut
Later on I realized that the video about TSP-BB taked about the Upper bound and added that mechanism aswell. At this point I started realizing why I'm asked for a "good enough" solution. There is no way I could compute something that gives the best exact solution with most common resources.

### branch-cut-aprox-v1
Back to research, the easy solution was Nearest Neighbor (NN), but that felt to me like too much computation skipping, the result was really fast but could be usually improved. Looking at my code and how the data was growing, I realized that on most of cases, there is no way that the worse branches on a branch may contain a solution, the triangle inequality was a "good enought" reson for me that would yeld a good solution for most of this cases, there could be a return route that covers that nodes later on, so I implemented a static value to just follow the best `L` routes and disregard the other ones.

If ``L = 1``, you have a NN optimization. But with `L = 2`, still was not able to make my computer run a 24 city solution because at some point, the edges that had high cost with low amount of travels would get at the start of the queue, so the ones that have way more jumps but with slighly more distance were not being evaluated until later on creating possibly bad branches that would be evaluated anyways taking a lot of memory space.

### branch-cut-aprox-v2
In order to get a better performance I try to avoid taking branches that have way less jumps done than the one with most travels. Here I could have made a cool heuristic for priority on the queue based on the weight, the distance and the amount of jumps. I just added a limitation(`D`) to avoid computing/adding branches to the queue when the have way smaller amount of jumps than the one that has most jumps.

What I actually did is to stop the time investment on algoritm improvement / optimization. I could try some heuristics I found so far (*2-opt/k-opt*) but I think it's not ok to keep doing so right now as I have spent more than 15 hours.

### calibration, test/test-props
Finally I tried to calibrate good parameter values (`L`, `D`) that gives good enough solutions that fit into time and my own memory limitations. Increasing `D` gives better results than increasing `L`, actually increasing `L` yelds faster into an out of memory. So increasing the amount of jump differences betweeen the branches on the queue is more effective than increasing the amount of branches to create when evaluating a branch.

### OOM limitations:
Some testing of diffent values yeld the following limitations:
* `L = 1` (NN) gives an instant result but as good as I would like
* `L = 3` I get OOM With `D > 4`
* `L > 3` didn't yeld any better results as I had to lower `D`
* `L = 2` I was able to run with `D < 7` to get the best possible result i was able to archieve with a distance of ~881.427

With 5GB of memory, I was able to run `L = 2, D = 6` that gave a result under 8 minutes

For different time limitations, results but less aproximate:
* 120 sec: `L = 2, D = 5`, 90 seconds, distance: 905.180
* 30 sec: `L = 2, D = 4`, 81 seconds, distance: 905.180
* 10 sec: `L = 2, D = 3`, 7 seconds, distance 913.189
* 2 sec: `L = 1, D = X`, 1.5 seconds, distance 933.004. `D` is irelevant here

Best results lowering the memory limit:
* 2GB: `L = 2, D = 5`, 90 seconds, distance: 905.180
* 1GB: `L = 2, D = 5`, 90 seconds, distance: 905.180
* 512M: `L = 2, D = 4`, 19 seconds, distance 913.189
* 256M: `L = 2, D = 4`, 19 seconds, distance 913.189
* 128M (Default): `L = 2, D = 3`, 7 seconds, distance 913.189

---
## References:
1: https://en.m.wikipedia.org/wiki/Travelling_salesman_problem

2: https://www.youtube.com/watch?v=XaXsJJh-Q5Y

3: https://cs.indstate.edu/cpothineni/alg.pdf

4: https://ideone.com/0TBgxr

5: https://cs.stackexchange.com/questions/43549/what-tsp-variant-doesnt-return-to-start-point

[1]: https://en.m.wikipedia.org/wiki/Travelling_salesman_problem
[2]: https://www.youtube.com/watch?v=XaXsJJh-Q5Y
[3]: https://cs.indstate.edu/cpothineni/alg.pdf
[4]: https://ideone.com/0TBgxr
[5]: https://cs.stackexchange.com/questions/43549/what-tsp-variant-doesnt-return-to-start-point
