# names
Dealing with names from wikidata on a website

# Building the database
## Getting surnames (new method)
Using sparql and the P1705 property, one can get ~300 surnames (as of April 2020) very quickly. No need to follow the previous method that was quite time consuming. Some records are missing but it should be considered enough.

First thing is, using https://query.wikidata.org/ to run the following query : 

```
SELECT DISTINCT ?item ?itemLabel ?language
WHERE 
{
  ?item wdt:P31 wd:Q101352.
  ?item wdt:P1705 ?itemLabel.
  BIND (LANG(?itemLabel) AS ?language)
}
```

Then use the Download as TSV File. On 13th April 2020 this file contains 293637 lines. 

## Getting surnames (old method)
We are using the wdumps tool from https://tools.wmflabs.org with the following settings (this is a very long process, more than 6 hours as of 2020) : 

- Basic filter : P31 = Q101352 non deprecated;
- Additional settings : only keep labels;

The JSON code for the query should look like :
```json
{
  "descriptions": false,
  "labels": true,
  "version": "1",
  "entities": [
    {
      "type": "item",
      "properties": [
        {
          "type": "entityid",
          "property": "P31",
          "rank": "non-deprecated",
          "id": 1,
          "value": "Q101352"
        }
      ],
      "id": 2
    }
  ],
  "meta": true,
  "aliases": false,
  "sitelinks": false,
  "statements": [
    {
      "qualifiers": false,
      "references": false,
      "rank": "all",
      "simple": true,
      "id": 0,
      "full": false
    }
  ]
}
```

From the downloaded file, we only get the labels:

```grep "http://www.w3.org/2000/01/rdf-schema#label" wdump-54.nt > labels.nt```

From this labels file we run the following command :
  
```cat labels.nt | awk '/^\s*[^#]/ { ORS=""; print $1 "\t" $3 " "; print "\n" }' | sed -E 's/"(.*)"@.*$/\1/g' | sed -E "s#<http://www.wikidata.org/entity/##g" | sed -E "s/>//g" | grep -v "(" | sort | uniq > labels_for_import.csv```

Then we prepare the file:

```sed '/^.\{255\}./d' labels_for_import.csv > labels.csv```

To load the created file into database, run the following command:

```LOAD DATA INFILE "/var/lib/mysql-files/labels.csv" INTO TABLE surname CHARACTER set 'utf8mb4' FIELDS TERMINATED BY '\t' (Q, label);```

# Importing into database
 

## Cleaning database
```sql
DELETE FROM `surname` where convert(label using latin1) != label;
DELETE FROM `forename` where convert(label using "latin1") != label;
DELETE FROM `surname` where LENGTH(label) <= 3;
DELETE FROM `surname` where label like '"%';
DELETE FROM `forename` where LENGTH(label) <= 3;
```