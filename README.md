# Benchmark Swoole PHP extension

I created this repository for testing the performance of [Swoole](https://www.swoole.co.uk/)
PHP extension.

At the moment, it contains an experiment that I did to build a static web server
using the `enable_static_handler` feature of Swoole ([static.php](static.php))
and an alternative approach to handle static files using the [Swoole\HTTP\Response::sendfile](http://php.net/manual/en/swoole-server.sendfile.php)
function ([static_sendfile.php](static_sendfile.php)).

## Usage

You need to use [docker](https://www.docker.com/) to setup the environment.

You need to build the docker containers using the following command:

```bash
docker-compose build
```

After, you can run the environment using the command:

```bash
docker-compose up -d
```

The `-d` option run containers in the background. This command will execute
two containers running the PHP scripts on localhost using the following ports:

- `static.php` on http://localhost:8080
- `static_sendfile.php` on http://localhost:8088

## Benchmark

You can benchmark the performance of Swoole using a PDF file (test.pdf) that I
provided for testing. You can use any benchmark HTTP tool, I used [wrk](https://github.com/wg/wrk)
testing 10 concurrent clients for a total of 1000 connections.

I run the experiment using Swoole 4.0.4 with PHP 7.2.8 on Intel i5 CPU with 16
GB RAM.

Here the results:

```
wrk -c 1000 -t 10 http://localhost:8080/test.pdf

Running 10s test @ http://localhost:8080/test.pdf
  10 threads and 1000 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency    79.54ms  165.63ms   1.88s    93.90%
    Req/Sec   624.99    247.98     2.19k    75.41%
  61429 requests in 10.09s, 5.88GB read
  Socket errors: connect 0, read 61417, write 0, timeout 15
Requests/sec:   6086.00
Transfer/sec:    596.20MB
```

```
wrk -c 1000 -t 10 http://localhost:8088/test.pdf
Running 10s test @ http://localhost:8088/test.pdf
  10 threads and 1000 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency   109.42ms   85.66ms   1.16s    95.49%
    Req/Sec     0.95k   268.90     3.11k    90.75%
  92305 requests in 10.07s, 8.83GB read
Requests/sec:   9163.40
Transfer/sec:      0.88GB
```

This result shows that the [static_sendfile.php](static_sendfile.php) scripts
is about **47% faster** than [static.php](static.php).

This is an interesting result and I see a lot of potential improvements in the
existing C implementation for static files in Swoole. For instance, adding a
simple caching system as I did in [static_sendfile.php](static_sendfile.php).
