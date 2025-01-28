{{ '#!/bin/sh' }}
ulimit -v unlimited
cd {{ $approot }} && env RUBY_GLOBAL_METHOD_CACHE_SIZE=131072 \
	LD_PRELOAD=/usr/lib64/libjemalloc.so.1 \
	UNICORN_SIDEKIQS=1 \
	UNICORN_PORT={{ $port }} \
	rbenv exec bundle exec unicorn -E ${RAILS_ENV:-production} -c config/unicorn.conf.rb -D