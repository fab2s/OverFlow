# OverFlow

[![CI](https://github.com/fab2s/NodalFlow/actions/workflows/ci.yml/badge.svg)](https://github.com/fab2s/NodalFlow/actions/workflows/ci.yml) [![QA](https://github.com/fab2s/NodalFlow/actions/workflows/qa.yml/badge.svg)](https://github.com/fab2s/NodalFlow/actions/workflows/qa.yml)  [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com)  [![License](https://poser.pugx.org/fab2s/nodalflow/license)](https://packagist.org/packages/fab2s/nodalflow)

`OverFlow` is a data-processing Workflow that can implement virtually any data workflow. It is designed around simple interfaces that specifies a `Flow` composed of executable `Nodes` and `Flows`. `Nodes` can be scalar (execute a task in a flow) or be generators (generate many results). They accept a single parameter as argument and can be set to pass or not their result as an argument for the next node.
`Flows` also accept one argument and may be set to pass their result to be used or not as an argument for their first Node.

```
+--------------------------+Flow Execution+----------------------------->

+-----------------+        +------------------+         +---------------+
|   scalar node   +-------->  generator node  +--------->   next node   +-------->...
+-----------------+        +------------------+         +---------------+
                                              |
                                              |         +---------------+
                                              +--------->   next node   +-------->...
                                              |         +---------------+
                                              |
                                              |         +---------------+
                                              +--------->   next node   +-------->...
                                              |         +---------------+
                                              |
                                              +--------->...

```

`Nodes` are linked together by the fact they return a value or not. When a node is returning a value (by declaration), it will be used as argument to the next node (but not necessarily used by it). When it doesn't, the current parameter (if any) will be used as argument by the next node, and so on until one node returns a result intended to be used as argument to the next node.

```
+--------+ Result 1 +--------+ Result 3
| Node 1 +----+-----> Node 3 +--------->...
+--------+    |     +--------+
              |
              |
         +----v---+
         | Node 2 |
         +--------+

```

In this flow, as node 2 (which may as well be a whole flow or branch) is not returning a value, it is executed "outside" of the main execution line.

In other words, `OverFlow` implements a directed graph structure in the form of a tree composed of nodes that can be, but not always are, branches or leaves. 

`OverFlow` also goes beyond that by allowing any `Flow` or `Node` to send whatever parameter to any part of any `Flow` alive within the same PHP process. The feature shares similarities with the `Generator`'s [`sendTo()`](/docs/usage.md#the-sendto-methods) method and makes it possible to turn `Flows` into _executable networks_ of `Nodes` (and `Flows`).

```
+-------------------------+-------+----------+
|               |-->      |       |          |
+-+Node1+->tNode|-->Node3+> bNode +-->NodeN+->
|FlowA       ^  |-->      |   |   |          |
+------------|----------------|--------------+
             |            |   v   |
             |            | Node1 |
             |            |   |   |
             |            |   v   |
             +---sendTo()-+ Node2 |
                          | +-+-+ |
                          | | | | |
                          | v v v |
                          | Node3 |
                          +---|--------------+
                          |   v   |          |
                          | bNode +-->Node1+->
                          |   |   |     |    |
                          +---|--------------+
                          |   |   |     |
                          +---v---+     |
                                        |
               +-------sendTo()---------+
               |
 +-------------|----------------+
 |             v                |
 +--Node1-->Node2-->NodeN--...+->
 |  FlowB                       |
 +------------------------------+
```

`OverFlow` aims at organizing and simplifying data processing workflow's where arbitrary amount of data may come from various generators, pass through several data processors and / or end up in various places and formats. But it can as well be the foundation to organizing pretty much any sequence of tasks (`OverFlow` could easily become Turing complete after all). It makes it possible to dynamically configure and execute complex scenario in an organized and repeatable manner (`OverFlow` is [serializable](/docs/serialization.md)). And even more important, to write `Nodes` that will be reusable in any other workflow you may think of.

`OverFlow` enforces minimalistic requirements upon nodes. This means that in most cases, you should extend `OverFlow` to implement the required constraints and grammar for your use case.

`OverFlow` shares conceptual similarities with [Transducers](https://clojure.org/reference/transducers) (if you are interested, also have a look at [Transducers PHP](https://github.com/mtdowling/transducers.php)) as it allow basic interaction chaining, especially when dealing with `ScalarNodes`, but the comparison diverges quickly.

## OverFlow Documentation

## Installation

`OverFlow` can be installed using composer:

```
composer require "fab2s/OverFlow"
```

## Requirements

`OverFlow` is tested against 8.1 and 8.2

## Contributing

Contributions are welcome, do not hesitate to open issues and submit pull requests.

## License

`OverFlow` is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
