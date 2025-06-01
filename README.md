# keoir-restful, a simple PHP restful framework
A PHP restful framework, using MySQL and the Red Bean PHP ORM for data management.

## Models
Models are for data, and are comprised of a key and a default value along with settings. When you begin to create objects with the create endpoint for the model it will begin to make the database schema using the popular PHP ORM RedBean. Models can be created by making a .php file in the models folder and calling new Model()

## Methods
Methods are actions in the api, for example a method would be create, update, or delete. The framework comes with a number of default methods. Methods can be created by adding a file to the methods folder and calling new Method() inside that.

[![Powered by DartNode](https://dartnode.com/branding/DN-Open-Source-sm.png)](https://dartnode.com "Powered by DartNode - Free VPS for Open Source")
