###################
How to make Codeigniter 3 RESTful API using POSTMAN
###################

we are going to create REST API in Codeigniter using POSTMAN in CodeIgniter 3 project. We will create the HTTP request like GET, POST, PUT, DELETE. 
RESTful Api in Codeigniter is also know as rest web services. We will be using one package called CodeIgniter RestServer to build this rest api in codeigniter 3.

*******************
 Securing the API
*******************

METHOD - 1:

Once your API is built. it needs securing. So the only users who have the access (Login Credentials) can get data through API.

1. To set the login credentials which is username and password

FILE: application/config/rest.php

***************************************************
$config['rest_valid_logins'] = ['auth' => '6283fe5ce9d6a'];
***************************************************

2. Change these details into 

// default
*****************************
$config['rest_auth'] = FALSE;
*****************************

// Change it to
*******************************
$config['rest_auth'] = 'basic';
*******************************

3. once we set the $config['rest_auth'] = 'basic' we have to give one more permission to access this as follows:

FILE: application/config/rest.php

// default
********************************
$config['auth_source'] = 'ldap';
********************************

// Change it to
****************************
$config['auth_source'] = '';
*****************************

Now, Open POSTMAN Software and choose Authorization and select TYPE as Basic Auth and then provide your username and password as shown above.

METHOD - 2:

1. set up the  X-API-KEY = '' we have to give one more permission to access this as follows:

FILE: application/config/rest.php

// default
***********************************
$config['rest_enable_keys'] = false;
***********************************

// change it to
***********************************
$config['rest_enable_keys'] = true;
***********************************

****************************
X-API-KEY = '5ece4797eaf5e';
*****************************

Step 4: Create database under name of `codeigniter-rest-api` and import it from the root directory of this project.

Step 5: Use the routes given below to send and receive the data through api using POSTMAN: paste in following path: application/config/routes.php

So, Let's get started.