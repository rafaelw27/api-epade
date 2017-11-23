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

GET http://d2d4a75a.ngrok.io/trucks

This route will return all Trucks that are currently in the Database.

**Get Truck:** 

GET http://d2d4a75a.ngrok.io/trucks/id

This route will return a specific truck based on a given id.

**Create Truck:**

POST http://d2d4a75a.ngrok.io/trucks

This route will enable you to create a truck on the database. You will have to provide the required fields of a truck to be able to create it. The required fields are: user_id, capacity,model,brand,plate.

**Update Truck:**

POST http://d2d4a75a.ngrok.io/trucks/id

This route will let you update or edit an existing truck based on a given id.

**Delete Truck:**

DELETE http://d2d4a75a.ngrok.io/trucks/id/delete

This route will let you delete an existing truck based on a given id.

### Reports Routes

**Get all Reports:**

GET http://d2d4a75a.ngrok.io/reports

This route will return all reports that are currently in the Database.

**Get Report:**

GET http://d2d4a75a.ngrok.io/reports/id

This route will return a specific report based on a given id.

**Create Report:**

POST http://d2d4a75a.ngrok.io/reports

This route will enable you to create a report on the database. You will have to provide the required fields of a driver to be able to create it.

**Update Report:**

POST http://d2d4a75a.ngrok.io/reports/id

This route will let you update or edit an existing report based on a given id.

**Delete Report:**

GET http://d2d4a75a.ngrok.io/reports/id/delete

This route will let you delete an existing report based on a given id.

### Drivers Routes

**Get all Drivers:**

GET http://d2d4a75a.ngrok.io/drivers

This route will return all drivers that are currently in the Database.

**Get Driver:** 

GET http://d2d4a75a.ngrok.io/drivers/id

This route will return a specific driver based on a given id.

**Create Driver:**

POST http://d2d4a75a.ngrok.io/drivers

This route will enable you to create a driver on the database. You will have to provide the required fields of a driver to be able to create it.

**Update Driver:**

POST http://d2d4a75a.ngrok.io/drivers/id

This route will let you update or edit an existing driver based on a given id.

**Delete Driver:**

DELETE http://d2d4a75a.ngrok.io/drivers/id/delete

This route will let you delete an existing driver based on a given id.



### Users Routes

**Get all Users:**

GET http://d2d4a75a.ngrok.io/users

This route will return all existing users in the database.

**Get User:**

GET http://d2d4a75a.ngrok.io/users/id

This route will return an existing user of the database based on a specific id.

**Create User:**

POST http://d2d4a75a.ngrok.io/users

This route will enable you to create a user on the database. You will have to provide the required fields of a user to be able to create it. The required fields are: first_name,last_name,email,phone,password,
user_type_id.

**Update User:**

POST http://d2d4a75a.ngrok.io/users/id

This route will let you update or edit an existing user based on a given id.

**Delete User:**

DELETE http://d2d4a75a.ngrok.io/users/id/delete

This route will let you delete an existing user based on a given id.

### Products Routes

**Get All Products:**

GET http://d2d4a75a.ngrok.io/products

This route will return all products on the database.

**Get Product:**

GET http://d2d4a75a.ngrok.io/products/id

This route will return a product based on a specific id.

**Create Product:**

POST http://d2d4a75a.ngrok.io/products

This route will enable you to create a product on the database. You will have to provide the required fields of a product to be able to create it. The required fields are : name, full_name, description,
type, taxable, maker ,unit_price, unit_volume, quantity.

**Update Product:**

POST http://d2d4a75a.ngrok.io/products/id

This route will let you update a product based on a specific id.

**Delete Product:**

GET http://d2d4a75a.ngrok.io/products/id/delete

This route will let you change the status of a product from "active" to "inactive".

### Vendors Routes

**Get All Vendors:**

GET http://d2d4a75a.ngrok.io/vendors

This route will return all vendors on the database.

**Get Vendor:**

GET http://d2d4a75a.ngrok.io/vendors/id

This route will return a vendor based on a specific id.


### Clients Routes

**Get All Clients:**

GET http://d2d4a75a.ngrok.io/clients

This route will return all clients on the database.

**Get Client:**

GET http://d2d4a75a.ngrok.io/clients/id

This route will return a client based on a specific id.

### Routes(Rutas) Routes

**Get All Routes:**

GET http://d2d4a75a.ngrok.io/routes

This route will return all routes on the database.

**Get Route:**

GET http://d2d4a75a.ngrok.io/routes/id

This route will return a route based on a specific id.

### Orders Routes

**Create Order:**

POST http://d2d4a75a.ngrok.io/orders

This route will enable you to create an order on the database. You will have to provide the required fields of a product to be able to create it.

**Get All Orders:**

GET http://d2d4a75a.ngrok.io/orders

This route will return all orders on the database.

**Get Order:**

GET http://d2d4a75a.ngrok.io/orders/id

This route will return an order based on a specific id.

**Get Orders by Client:**

GET http://d2d4a75a.ngrok.io/orders/id/clients

This route will return all orders of a specific client.

**Change Order Status:**

POST http://d2d4a75a.ngrok.io/orders/id/status

This route will let you change the status of an order based on a given id.

**Get Orders by Driver:**

GET http://d2d4a75a.ngrok.io/orders/id/drivers

This route will return all orders of a specific driver.

### Login Route

**Login:**

POST http://d2d4a75a.ngrok.io/login

This is the route for the Login. The required fields for login are: email and password.

POST http://d2d4a75a.ngrok.io/logout

This is the route for the logout. This will destroy the current session.

### Extra Routes

**Sync all Vendors in our Database**

GET http://d2d4a75a.ngrok.io/vendors/quickbooks

**Sync all Clients in our Database**

GET http://d2d4a75a.ngrok.io/clients/quickbooks