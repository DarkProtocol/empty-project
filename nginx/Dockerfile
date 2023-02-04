FROM knb-ru-backend:latest as backend

FROM nginx:1.19-alpine

# nginx user must be in www-data group
# to have access to PHP socket
RUN adduser nginx www-data

COPY ./templates /etc/nginx/templates
COPY --from=backend /app/public /app/public

WORKDIR /app
