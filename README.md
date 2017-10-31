# EPADE Project

## Entities & Fields

Here are the entities that are currently in use in our database

### Products 
**Fields**

* id
* name
* full_name
* description
* active
* type
* taxable
* maker
* unit_price
* unit_volume
* quantity

### Users
**Fields**

* id
* user_type_id
* first_name
* last_name
* email
* password
* phone



### Trucks
**Fields**

* id
* user_id
* brand
* model
* capacity
* plate


## API Routes

This routes will enable you to connect to our database and perform functions.

### Truck Routes

**Get all Trucks:**

GET http://api-epade.io/trucks

This route will return all Trucks that are currently in the Database.

**Get Truck:** 

GET http://api-epade.io/trucks/id

This route will return a specific truck based on a given id.

**Create Truck:**

POST http://api-epade.io/trucks

This route will enable you to create a truck on the database. You will have to provide the required fields of a truck to be able to create it. The required fields are: user_id, capacity,model,brand,plate.

**Update Truck:**

POST http://api-epade.io/trucks/id

This route will let you update or edit an existing truck based on a given id.

**Delete Truck:**

DELETE http://api-epade.io/trucks/id

This route will let you delete an existing truck based on a given id.

### Users Routes

**Get all Users:**

GET http://api-epade.io/users

This route will return all existing users in the database.

**Get User:**

GET http://api-epade.io/users/id

This route will return an existing user of the database based on a specific id.

**Create User:**

POST http://api-epade.io/users

This route will enable you to create a user on the database. You will have to provide the required fields of a user to be able to create it. The required fields are: first_name,last_name,email,phone,password,
user_type_id.

**Update User:**

POST http://api-epade.io/users/id

This route will let you update or edit an existing user based on a given id.

**Delete User:**

DELETE http://api-epade.io/users/id

This route will let you delete an existing user based on a given id.

### Products Routes

**Get All Products:**

GET http://api-epade.io/products

This route will return all products on the database.

**Get Product:**

GET http://api-epade.io/products/id

This route will return a product based on a specific id.

**Create Product:**

POST http://api-epade.io/products

This route will enable you to create a product on the database. You will have to provide the required fields of a product to be able to create it. The required fields are : name, full_name, description,
type, taxable, maker ,unit_price, unit_volume, quantity.

**Update Product:**

POST http://api-epade.io/products/id

This route will let you update a user based on a specific id.

**Delete Product:**

DELETE http://api-epade.io/products/id

This route will let you delete a user based on a specific id.

### Login Route

**Login:**

<<<<<<< HEAD
POST http://api-epade.io/login

This is the route for the Login. The required fields for login are: email and password.
=======
This is the route for the Login.

>>>>>>> 3247fa4bad82543e440b2c7df0c6638d5aa17f07
