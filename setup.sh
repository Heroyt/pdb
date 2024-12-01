#!/bin/bash

API_BASE_URL="https://localhost/command"
DATA_FILE="data.json"

jq -c '.materials[]' "$DATA_FILE" | while read -r material; do
  curl -k -X POST "$API_BASE_URL/material" \
       -H "Content-Type: application/json" \
       -H "Accept: application/json" \
       -d "$material"
 echo ""
done

jq -c '.factories[]' "$DATA_FILE" | while read -r factory; do
  curl -k -X POST "$API_BASE_URL/factory" \
       -H "Content-Type: application/json" \
       -H "Accept: application/json" \
       -d "$factory"
 echo ""
done

sleep 10

jq -c '.processes[]' "$DATA_FILE" | while read -r process; do
  id=$(echo "$process" | jq -r '.factory')
  echo "$process" | jq -c '.data[]' | while read -r pdata; do
    curl -k -X POST "$API_BASE_URL/factory/$id/process" \
         -H "Content-Type: application/json" \
         -H "Accept: application/json" \
         -d "$pdata"
     echo ""
  done
done

jq -c '.connections[]' "$DATA_FILE" | while read -r connection; do
  curl -k -X POST "$API_BASE_URL/connection" \
       -H "Content-Type: application/json" \
       -H "Accept: application/json" \
       -d "$connection"
 echo ""
done

sleep 10

jq -c '.maxStorage[]' "$DATA_FILE" | while read -r storage; do
  id=$(echo "$storage" | jq -r '.id')
  data=$(echo "$storage" | jq -r '.data')
  curl -k -X PUT "$API_BASE_URL/connection/$id/storage-max" \
       -H "Content-Type: application/json" \
       -H "Accept: application/json" \
       -d "$data"
 echo ""
done

jq -cr '.assign[]' "$DATA_FILE" | while read -r id; do
  curl -k -X POST "$API_BASE_URL/connection/$id/assign" \
       -H "Accept: application/json"
 echo ""
done