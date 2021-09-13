## Game API
Application allowing adding cards to the deck. It consists of two HTTP APIs written in RESTful manner:
* Cards catalog API
* Deck API

### Cards catalog API:
The API exposes methods to:
* Create a new card with unique title
* Remove a card
* Update card title and/or power
* Show the list of the cards with 3 card per page

### User deck API
The API exposes methods to:
* Create a deck with unlimited amount of decks per user
* Add a card to the deck. No more than 2 unique cards and no more 10 cards in total
* Remove card from the deck
* List all the cards in the deck with total power of all cards in the deck

## How to start
* Install [Docker](https://docs.docker.com/install/) and [Docker Compose](https://docs.docker.com/compose/install/)
* Clone repository
* Enter the repository root, build and run containers
    ```sh
    docker-compose up -d
    ```
* Install composer dependencies by running
  ```sh
  docker-compose exec php /var/www/html/composer_install.sh
    ```
* Start events consumer
  ```sh
  docker-compose exec php /var/www/html/start_consumer.sh
    ```
  
* API available on `http://127.0.0.1:8888`

## To run tests
* Get get container id
    ```sh
    docker ps
    ```
* Get into container with id from previous step
    ```sh
    docker exec -ti <<your_id_here>> /bin/sh
    ```
* Enter `/var/www/catalog` and run
    ```sh
    php vendor/bin/phpunit --configuration phpunit.xml.dist
    ```

## Notes
* nginx works on `8888` port
* php works on `9002` port
* mysql works on `3307` port

If they are busy change them in `docker-compose.yml`

## How to use
* [Requests](doc/requests.md)

## Design marks
* [Architecture](doc/arhitecture.md)