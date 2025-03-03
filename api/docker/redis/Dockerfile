FROM redis:7.4-alpine

#RUN mkdir -p /var/run/redis
#RUN chmod 777 /var/run/redis
COPY ./api/docker/redis/redis.conf /etc/redis.conf

ENTRYPOINT ["docker-entrypoint.sh", "/etc/redis.conf"]

