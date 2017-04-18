wget https://download.elastic.co/elasticsearch/elasticsearch/elasticsearch-1.7.2.tar.gz
tar -xvf elasticsearch-1.7.2.tar.gz
nohup elasticsearch-1.7.2/bin/elasticsearch > /dev/null 2>&1 &
sleep 10 && wget --waitretry=5 --retry-connrefused -v http://127.0.0.1:9200/