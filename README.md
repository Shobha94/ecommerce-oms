1.composer install

2.Configure .env

3.php artisan key:generate

4.php artisan migrate --seed

5.Run worker: php artisan queue:work

6.Start server: php artisan serve

7.Login using seeded admin: admin1@example.com / password or create new via /api/register.

8.Import postman_collection.json and set {{base}} + {{token}}.

9.Roles

admin: manage categories/products/orders status.

customer: cart & orders.

10.Caching

/api/products cached 5 mins; writes flush cache (swap to Redis & tags for prod).

11.Testing

php artisan test (aim for 85%+ — add more coverage around payments, filters, guards).