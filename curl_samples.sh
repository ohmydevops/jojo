#!/usr/bin/env bash
#########
# How to run: time ./curl_samples.sh 
#########

for ((counter=1; counter<=1000; counter++))
do
  curl --silent --output /dev/null --write-out "$counter - %{http_code}\n" "http://localhost:8000/" &
done

wait