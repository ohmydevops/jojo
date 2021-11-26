#!/usr/bin/env bash

for ((counter=1; counter<=1000; counter++))
do
  time curl --silent -X GET "http://localhost:8000/" &
done
