#!/bin/sh

cp ~/.ssh/*.pub ./ssh_keys.pub
docker build -t lcwo:latest .
