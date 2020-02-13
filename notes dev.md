# Récupération des données
La récupération des noms de familles se fait à l'aide de : 
https://tools.wmflabs.org/wdumps/dump/53


Pour récupérer les labels d'une liste : 

cat Q28.nt | rdf-objects | sed -E 's/"(.*)"@.*/\1/g' | sort | uniq

avec 
alias rdf-objects="awk '/^\s*[^#]/ { ORS=\"\"; for (i=3;i<=NF-1;i++) print \$i \" \"; print \"\n\" }' | uniq"



<http://www.wikidata.org/entity/Q28657>	Fotopoulos
<http://www.wikidata.org/entity/Q28657>	Φωτόπουλος
<http://www.wikidata.org/entity/Q28657>	Фотопулос


cat labels.nt | awk '/^\s*[^#]/ { ORS=""; print $1 "\t" $3 " "; print "\n" }' | sed -E 's/"(.*)"@.*$/\1/g' | sed -E "s#<http://www.wikidata.org/entity/##g" | sed -E "s/>//g" | sort | uniq



# Préparation du fichier NNT

sed '/^.\{255\}./d' labels_for_import.csv > labels.csv

# Chargement de la base
depuis mysql : 
LOAD DATA INFILE "/var/lib/mysql-files/labels.csv" INTO TABLE surname CHARACTER set 'utf8mb4' FIELDS TERMINATED BY '\t' (Q, label);


# Nettoyage base
Supppression des surnames non latins :
DELETE FROM `surname` where convert(label using latin1) != label;
DELETE FROM `forename` where convert(label using "latin1") != label;
DELETE FROM `surname` where LENGTH(label) <= 3;
DELETE FROM `forename` where LENGTH(label) <= 3;