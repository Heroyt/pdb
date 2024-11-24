#!/bin/bash

echo "Entry: $SHELL $0"

composer dump-autoload

# Clear DI, model and info cache
./bin/console cache:clean -dmic
./bin/console install

# Cleanup restart.txt if not correctly removed to prevent immediate restart of container
if [ -f ./temp/restart.txt ]; then
  rm -f ./temp/restart.txt
fi

# Run project
echo 'Starting...'
echo "$PWD"
rr -v
cron &
rr serve -e -c .rr.yaml -p &

while true; do
  if [ -f ./temp/restart.txt ]; then
    echo "Restarting container..."

    # Remove the restart flag
    rm -f ./temp/restart.txt

    # Do any additional cleaning up if you need to.
    rr stop

    # exit the container - exit code is optional
    exit 0
  fi
  sleep 5
done