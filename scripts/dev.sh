#!/usr/bin/env bash

set -euo pipefail

cd "$(dirname "$0")/.."

mkdir -p 'attachments'
chmod 777 'attachments'
printf '' > 'logs.txt'
chmod 766 'logs.txt'
docker-compose up -d --build
bash 'scripts/build.sh' '--watch'
