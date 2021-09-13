## Requests
### Catalog
1. **Add card** to catalog

   Method
   `POST /cards`

   **Request**
   ```json
    {
        "title": "CardName",
        "power": 10
    }
    ```
   required:
    * title
    * power

   **Response** code 201 with header:

   `Location: /cards/2dc4829c-f565-467d-b010-4247904a9ac1`

2. **Update card** in catalog

   Method
   `PATCH /cards/{card_id}`

   **Request**
   ```json
    {
        "power": "1280"
    }
    ```
   required:
    * title or power

   notes:
    * title should be unique

   **Response** code 200 with no body

3. **Get card** from catalog

   Method
   `GET /cards/{card_id}`

   notes:
    * card_id is uuid format string, example `4fdec374-833d-485c-a142-eeeb30d733b7`

   **Response** code 200 with body
   ```json
    {
        "data": {
            "id": "4fdec374-833d-485c-a142-eeeb30d733b7",
            "title": "CardName",
            "power": 10
        }
    }
    ```

   If card doesn't exit will return 404 status code with body
    ```json
    {
      "message": "No card with id 4fdec374-833d-485c-a142-eeeb30d733b7 exist"
    }
    ```

4. **List of cards** from catalog

   Method
   `GET /cards`

   notes:
    * page_id can be added

   **Response** code 200 with body
   ```json
    {
       "data":{
          "items":[
             {
                "id":"2dc4829c-f565-467d-b010-4247904a9ac1",
                "title":"title1",
                "power":10
             },
             {
                "id":"4fdec374-833d-485c-a142-eeeb30d733b5",
                "title":"title2",
                "power":2
             },
             {
                "id":"4fdec374-833d-485c-a142-eeeb30d733b6",
                "title":"title3",
                "power":0
             }
          ],
          "_links":{
             "next":"/cards/2"
          }
       }
    } 
   ```

   The next/previous page can be requested by `GET /cards/2`

   and response _links illustrate other navigation pages

   ```json
      "_links": {
            "prev": "/cards",
            "next": "/cards/3"
      }
   ```

### Deck
1. **Add deck**

   Method
   `POST /decks`

   **Request**
   ```json
    {
        "user_id": "4fdec374-833d-485c-a142-eeeb30d733b7"
    }
    ```
   required:
    * user_id

   notes:
    * user_id is uuid format string, example `4fdec374-833d-485c-a142-eeeb30d733b7`

   **Response** code 201 with header:

   `Location: /decks/5d7705e9-0b5b-4592-b24b-d8c6c034ac78`

2. **Add card** to deck

   Method
   `POST /decks/{deck_id}/cards`

   **Request**
   ```json
    {
        "card_id": "4fdec374-833d-485c-a142-eeeb30d733b7",
        "amount": 2
    }
    ```

   required:
    * deck_id
    * card_id

   notes:
    * deck_id is uuid format string, example `5d7705e9-0b5b-4592-b24b-d8c6c034ac78`
    * card_id is uuid format string, example `4fdec374-833d-485c-a142-eeeb30d733b7`

   **Response** code 200

3. **Delete card** from deck

   Method
   `DELETE /decks/{deck_id}/cards/{card_id}`

   **Request**
   ```json
    {
        "amount": 2
    }
    ```

   required:
    * deck_id
    * card_id

   notes:
    * deck_id is uuid format string, example `5d7705e9-0b5b-4592-b24b-d8c6c034ac78`
    * card_id is uuid format string, example `4fdec374-833d-485c-a142-eeeb30d733b7`
    * amount is 1 by default

   **Response** code 200

4. **Get cards** form deck (same as get deck)

   Method
   `/decks/{deck_id}`

   **Request**

   required:
    * deck_id

   notes:
    * deck_id is uuid format string, example `d1f5b45a-956c-4842-8554-978d38728c07`

   **Response** code 200 with body
    ```json
   {
      "data": {
          "id": "d1f5b45a-956c-4842-8554-978d38728c07",
          "user_id": "2dc4829c-f565-467d-b010-4247904a9ac1",
          "cards": [
                { "id": "4fdec374-833d-485c-a142-eeeb30d733b7", "title": "title1", "power": 2, "amount": 1, "is_deleted": true },
                { "id": "4fdec374-833d-485c-a142-eeeb30d733b6", "title": "title2", "power": 3, "amount": 3, "changes": [{"title": "new title"}] }
             ],
         "power": 11
      }
   }
    ```
   notes:
   ```json
   {
      "is_deleted": true, 
      "changes": []
   }
   ```    
    * `changes` contains data about card original fields that differ from the one in deck. It means that card was changed after adding to deck. For ex. `{"title": "new title"}` says than new card title in catalog is `new title`
    * `is_deleted` `true` means that card was deleted from catalog but presents in deck

   If deck doesn't exit will return 404 status code with body
    ```json
    {
      "message": "No deck with id 5058a6e6-1840-4f24-8f5d-0f151b3b3bfa exists"
    }
    ```

5. **Delete deck** with all cards

   Method
   `DELETE /decks/{deck_id}`

   required:
    * deck_id

   notes:
    * deck_id is uuid format string, example `5d7705e9-0b5b-4592-b24b-d8c6c034ac78`

   **Response** code 200