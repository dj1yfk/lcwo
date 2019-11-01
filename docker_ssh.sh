#!/bin/sh

IP=$( docker inspect -f "{{ .NetworkSettings.IPAddress }}" $( docker ps | grep lcwo | head -1 | awk '{print $1}' ) )

ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no root@$IP
