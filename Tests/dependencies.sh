wget https://download.elasticsearch.org/elasticsearch/release/org/elasticsearch/distribution/tar/elasticsearch/2.0.0/elasticsearch-2.0.0.tar.gz
tar -xvf elasticsearch-2.0.0.tar.gz
nohup elasticsearch-2.0.0/bin/elasticsearch > /dev/null 2>&1 &
sleep 10 && wget --waitretry=5 --retry-connrefused -v http://127.0.0.1:9200/
php -d memory_limit=-1 /usr/local/bin/composer require victoire/title-widget dev-master