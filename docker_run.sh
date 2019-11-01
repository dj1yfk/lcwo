#!/bin/sh

# our MySQL data directory /var/lib/mysql will be in a volume
# called lcwo_data. If this doesn't exist yet, it will be 
# populated with the pre-filled tables. On subsequent starts
# the data from the volume will be used.

docker run -i --mount src=lcwo_data,target=/var/lib/mysql -t --rm=true -p 8000:80 lcwo:latest
