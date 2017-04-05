wget https://download.elastic.co/elasticsearch/release/org/elasticsearch/distribution/tar/elasticsearch/2.4.3/elasticsearch-2.4.3.tar.gz
tar -xvf elasticsearch-2.4.3.tar.gz
nohup elasticsearch-2.4.3/bin/elasticsearch > /dev/null 2>&1 &
sleep 10 && wget --waitretry=5 --retry-connrefused -v http://127.0.0.1:9200/