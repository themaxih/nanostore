php bin/console doctrine:database:drop --force --env=test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:drop --env=test
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load --env=test --no-interaction

mkdir data
echo "Fetching CSV Files..."
CSV_URL="https://docs.google.com/spreadsheets/d/1yX1OgaK1LA89VnEDBPl4ESPXrbFnLnAZBfcgs8OKRYU/gviz/tq?tqx=out:csv&sheet=Telephones"
curl -L "$CSV_URL" -o "data/telephones.csv" -s

CSV_URL="https://docs.google.com/spreadsheets/d/1yX1OgaK1LA89VnEDBPl4ESPXrbFnLnAZBfcgs8OKRYU/gviz/tq?tqx=out:csv&sheet=Peripheriques"
curl -L "$CSV_URL" -o "data/peripheriques.csv" -s

CSV_URL="https://docs.google.com/spreadsheets/d/1yX1OgaK1LA89VnEDBPl4ESPXrbFnLnAZBfcgs8OKRYU/gviz/tq?tqx=out:csv&sheet=Tablettes"
curl -L "$CSV_URL" -o "data/tablettes.csv" -s

CSV_URL="https://docs.google.com/spreadsheets/d/1yX1OgaK1LA89VnEDBPl4ESPXrbFnLnAZBfcgs8OKRYU/gviz/tq?tqx=out:csv&sheet=Ordinateurs"
curl -L "$CSV_URL" -o "data/ordinateurs.csv" -s

CSV_URL="https://docs.google.com/spreadsheets/d/1yX1OgaK1LA89VnEDBPl4ESPXrbFnLnAZBfcgs8OKRYU/gviz/tq?tqx=out:csv&sheet=Categories"
curl -L "$CSV_URL" -o "data/categorie.csv" -s

CSV_URL="https://docs.google.com/spreadsheets/d/1yX1OgaK1LA89VnEDBPl4ESPXrbFnLnAZBfcgs8OKRYU/gviz/tq?tqx=out:csv&sheet=SousCategories"
curl -L "$CSV_URL" -o "data/sous_categorie.csv" -s

echo "Successfully fetched 6 csv files into data/."


cd data
cat telephones.csv peripheriques.csv tablettes.csv ordinateurs.csv > produits.csv
cd ..

mariadb -u root --password=LYBmq4z0 --local-infile=1 <<EOF
use app_test
LOAD DATA LOCAL INFILE 'data/categorie.csv' INTO TABLE categorie FIELDS TERMINATED BY ',' ENCLOSED BY '"' IGNORE 1 LINES (name, href_name);
LOAD DATA LOCAL INFILE 'data/sous_categorie.csv' INTO TABLE sous_categorie FIELDS TERMINATED BY ',' ENCLOSED BY '"' IGNORE 1 LINES (name, category_id, href_name);
LOAD DATA LOCAL INFILE 'data/produits.csv' INTO TABLE produit FIELDS TERMINATED BY ',' ENCLOSED BY '"' IGNORE 1 LINES (sub_category, title, description, quantity_left, price, nom_image);
DELETE FROM produit WHERE title = '';
EOF

rm -rf data/*
rmdir data

echo -e "\e[1;42m [OK] Succesfully imported data \e[0m"
