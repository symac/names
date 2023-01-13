# names
Dealing with names from wikidata on a website

# Building the database
To populate the database, one has to run queries against SPARQL endpoint, download the results, then load them using commands. The SPARQL query to be run is given by each command.

The commands that have to be run are : 

- ``` symfony console app:populate-forename```
- ``` symfony console app:get-genders```
- ``` symfony console app:populate-surname```
- ``` symfony console app:clean-db```