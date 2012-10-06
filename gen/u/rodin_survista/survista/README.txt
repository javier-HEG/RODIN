SUrvista Rdf VISualizaTion Application
======================================

1. What does it do?

The SUrvista Rdf VISualizaTion Application visualizes RDF data stored in a
Semsol ARC store.
In RDF, the smallest piece of information is a triple, i.e.: Subject Predicate Object
Predicates are either data or object relations. By treating subjects and objects
as nodes and object relations as edges, one can create a directed graph out of RDF data.

2. Installation

2.1 Requirements

- PHP, tested on version 5.2.12
- Apache, tested on version 2.2.14 (Win32)
- MySQL client, tested on version 5.0.51a
- MySQL server, tested on version 5.1.44-community
- ARC RDF Classes for PHP (http://arc.semsol.org), tested on version 2.0.0 (495d10b)
- JavaScript InfoVis Toolkit (http://thejit.org), tested on version 2.0.0b
- JQuery JavaScript Library  (http://jquery.com), tested on version 1.4.4

2.2 Prerequisites

- An ARC store filled with data
- Apache/MySQL/PHP stack set up

2.3 Setup

1) Copy the files to a folder accessible over the web

2) Prepare a folder per store you want to visualize, i.e. "dbpedia":
    dbpedia/
        index.php - Demo visualization
        config.inc.php - Store/Data specific configuration
        ajax.php - Ajax service endpoint
        info.php - Helpfile for configuration

3) Configure the parameters specific to your data (config.inc.php)
    Store access: db_name, db_user, db_pwd, store_name

    Try calling the application to visualize some of your data:
        index.php -> Search for an URI you know exists in your data

4) RDF specific configuration (config.inc.php)
    Now that your application basically works, you can start configuring the
    visualization specific to your data:
        - Relations used for labeling:
            By default, Survista uses http://www.w3.org/2000/01/rdf-schema#label
            It is possible to additionally add other label relations:
                setPreferredLabelProperty
                addAlternativeLabelPropterty
            Check by querying a resource, it's label should be used instead of the URI

        - Additional labels
            If your RDF data misses labels for nodes or edges, those can be added
            manually

    Since RDF data (sometimes) contains triples about its own structure and data
    you don't want to show our users, Survista offers a filtering mechanism and
    already has a set of filters preconfigured. You can customize or overwrite
    the filtering rules:
        - Node filtering
            Depending on your data, you don't want the visualization to include
            every possible RDF resource (subjects and objects), but only those
            nodes interesting to your users.
            Survista offers an allow/deny filtering mechanism.
            The filter rules are checked in sequence. Resources passing all rules
            can finally be allowed or denied.

            If you don't know what data is in or gets into your store: whitelist

            Check and refine your setup by quering the nodes (take care, some
            methods might query a lot of data!):
                info.php -> store->node_stats()

        - Edge filtering
            Works the same way as node filtering

            Check and refine your setup by quering the edges:
                info.php -> store->edge_stats();

5) You should now be able to:
- Query n resources by URIs
- Query resources by searching in the labels
- Navigate in the graph by double-clicking
- Add terms to the inputfield by right-clicking on a node

3. Known problems

3.1 Number of objects queried
Depending on how many resources are queried at once and how many relations
these resources have, many nodes and edges are queried.
Both querying their labels and adjacencies takes time.
Additionally, the visualization area gets filled up and the graph gets confusing