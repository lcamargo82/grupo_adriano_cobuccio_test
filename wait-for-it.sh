#!/bin/bash

host="$1"
port="$2"
shift 2
cmd="$@"

echo "⏳ Aguardando conexão com $host:$port..."

while ! nc -z "$host" "$port"; do
  sleep 1
done

echo "✅ Conexão estabelecida com $host:$port, executando comando..."

exec $cmd
