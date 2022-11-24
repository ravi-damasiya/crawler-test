# Crawler Test

Clone project via [Crawler Test](https://github.com/ravi-damasiya/crawler-test.git)

Build and up docker via `docker-compose up -d --build`

Install Composer `docker exec crawler_test_php composer install`

Run Migration `docker exec crawler_test_php php bin/console doctrine:migration:migrate`

Run Fixture `docker exec crawler_test_php php bin/console doctrine:fixture:load -n`

Open project on browser [http://localhost:8080](http://localhost:8080)

Use below credentials to login in to system
  1. `username: admin // password: admin_1234`
  2. `username: moderator // password: moderator_1234`

Use command to sync news `docker exec crawler_test_php php bin/console app:sync-news`

Use command to consume all rabbitMq process `docker exec crawler_test_php php bin/console messenger:consume async -vv`