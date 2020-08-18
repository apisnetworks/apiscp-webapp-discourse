#!/bin/sh

cd {{ $approot }} && env RUBY_GLOBAL_METHOD_CACHE_SIZE=131072 \
	LD_PRELOAD=/usr/lib64/libjemalloc.so.1 \
	RAILS_ENV=${RAILS_ENV:-production} \
	UNICORN_SIDEKIQS=1 \
	rbenv exec ruby ./bin/unicorn -c config/unicorn.conf.rb -D -p {{ $port }}