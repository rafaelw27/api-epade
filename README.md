# EPADE Project
## Entities & Fields

Here are the entities that are currently in use in our database

### Products 
**Fields**

-id
-name
-full_name
-description
-active
-type
-taxable
-maker
-unit_price
-unit_volume
-quantity

### Users
**Fields**

-id
-user_type_id
-first_name
-last_name
-email
-password
-phone



### Trucks
**Fields**

-id
-user_id
-brand
-model
-capacity
-plate

## Routes

### Truck Routes

**Get all Trucks:**
$router->get('/trucks', [
    'Epade\Controllers\TrucksController',
    'getTrucks',
]);

This route will return all Trucks that are currently in the Database.

**Get Truck:** 
$router->get('/trucks/{id}', [
    'Epade\Controllers\TrucksController',
    'getTruck',
]);

This route will return a specific truck based on a given id.

**Create Truck:**
$router->post('/trucks', [
    'Epade\Controllers\TrucksController',
    'create',
]);

This route will enable you to create a truck on the database. You will have to provide the required fields of a truck to be able to create it.

**Update Truck:**
$router->post('/trucks/{id}', [
    'Epade\Controllers\TrucksController',
    'edit',
]);

This route will let you update or edit an existing truck based on a given id.

**Delete Truck:**
$router->delete('/trucks/{id}', [
    'Epade\Controllers\TrucksController',
    'delete',
]);

This route will let you delete an existing truck based on a given id.

### Users Routes

**Get all Users:**
$router->get('/users', [
    'Epade\Controllers\UsersController',
    'getUsers',
]);

This route will return all existing users in the database.

**Get User:**
$router->get('/users/{id}', [
    'Epade\Controllers\UsersController',
    'getUser',
]);

This route will return an existing user of the database based on a specific id.


**Get User:**
$router->get('/users/{id}', [
    'Epade\Controllers\UsersController',
    'getUser',
]);

This route will return an existing user of the database.

**Create User:**
$router->post('/users', [
    'Epade\Controllers\UsersController',
    'create',
]);

This route will enable you to create a user on the database. You will have to provide the required fields of a user to be able to create it.

**Update User:**
$router->post('/users/{id}', [
    'Epade\Controllers\UsersController',
    'edit',
]);

This route will let you update or edit an existing user based on a given id.

**Delete User:**
$router->delete('/users/{id}', [
    'Epade\Controllers\UsersController',
    'delete',
]);
This route will let you delete an existing user based on a given id.

### Products Routes

**Get All Products:**
$router->get('/products',[
    'Epade\Controllers\ProductsController',
    'getProducts',
]);

This route will return all products on the database.

**Get Product:**
$router->get('/products/{id}',[
    'Epade\Controllers\ProductsController',
    'getProduct',
]);

This route will return a product based on a specific id.

**Create Product:**
$router->post('/products', [
    'Epade\Controllers\ProductsController',
    'create',
]);

This route will enable you to create a product on the database. You will have to provide the required fields of a product to be able to create it.

**Update Product:**
$router->post('/products/{id}', [
    'Epade\Controllers\ProductsController',
    'edit',
]);

This route will let you update a user based on a specific id.

**Delete Product:**
$router->delete('/products/{id}',[
    'Epade\Controllers\ProductsController',
    'delete',
]);
This route will let you delete a user based on a specific id.

### Login Route

**Login:**
$router->post('/login', [
    'Epade\Controllers\UsersController',
    'login',
]);

This is the route for the Login.
