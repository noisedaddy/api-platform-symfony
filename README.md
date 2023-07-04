- https://github.com/GaryClarke/api-platform-crash-course
- Run tests: vendor/bin/phpunit tests
- Symfony server: symfony serve -d


- POST
- http://127.0.0.1:8001/api/products
- Body
{
"mpn": "random_mpn_123",
"name": "Laser guided pigion",
"description": "some desc",
"issueDate": "2022-01-31",
"manufacturer" : "/api/manufacturers/1"
}
- Headers
- {
  - Accept : application/ld+json
  - x-api-token: from db
  - Content-Type: application/ld+json
- }


- GET
- http://127.0.0.1:8001/api/products
- Headers
- {
    - Accept : application/ld+json
    - x-api-token: from db
    - Content-Type: application/ld+json
- }