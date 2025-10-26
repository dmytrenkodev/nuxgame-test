# NuxGame Test Task

## Running

1. Clone repository

 - ``` git clone https://github.com/dmytrenkodev/nuxgame-test.git ```

2. Go to 
 - ``` cd nuxgame-test ```

3. From root directory
 - ``` docker compose up -d --build ```

4.Init DB skeleton
 - ``` sh init_db.sh ```

5. Go to
 - ``` http://localhost:8080 ```

## Possible Improvements

- Add proper frontend styling using CSS or a framework (Bootstrap, Tailwind)
- Implement client-side validation for the registration form
- Add pagination or filtering for the lucky history
- Use a dedicated service class for Lucky calculation logic
- Implement user authentication instead of relying solely on tokens
- Add unit and integration tests for controllers and database interactions
- Use environment variables for database credentials instead of hardcoding
- Implement rate limiting to prevent abuse of the "I'm Feeling Lucky" button
- Add logging for actions like link regeneration, deactivation, and lucky draws
- Support multiple languages / localization
