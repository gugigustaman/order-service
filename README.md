# Order Service

This project is created to show how a flash sale on online store should be built regarding case of this precondition and also considering my experience on back-end programming.

**Table of Contents**
- [Precondition](#precondition)
- [Cause](#cause)
- [Requirements](#requirements)
- [Solution](#solution)
- [Stack and Process](#stack-and-process)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [API Documentation](#api-documentation)
- [Logging](#logging)
- [Error Handling](#error-handling)
- [Testing](#testing)

## Precondition

We are members of the engineering team of an online store. When we look at ratings for our online store application, we received the following facts:

1.  Customers were able to put items in their cart, check out, and then pay. After several days, many of our customers received calls from our Customer Service department stating that their orders have been canceled due to stock unavailability.
2.  These bad reviews generally come within a week after our 12.12 event, in which we held a large flash sale and set up other major discounts to promote our store.    

After checking in with our Customer Service and Order Processing departments, we received the following additional facts:

1.  Our inventory quantities are often misreported, and some items even go as far as having a negative inventory quantity.
2.  The misreported items are those that performed very well on our 12.12 event.
3.  Because of these misreported inventory quantities, the Order Processing department was unable to fulfill a lot of orders, and thus requested help from our Customer Service department to call our customers and notify them that we have had to cancel their orders.

## Cause

The negative stock is occurred because of there are more than one incoming payment requests for the carts with the same limited stock product at the nearly same time. As they come at the nearly same time, the handler gets the same number of available stock and continue the order process and of course decreasing the stock of every product requested on each orders. At the end, the system gets the product stock is negative because of the stock decrement.

## Requirements

There should be no more orders processed if the product is sold out so there will be no complaints from customers about their orders getting cancelled because of stock unavailability.

## Solution

To handle such case there should be a **queue** for processing incoming orders. Especially if we are dealing with products with limited stock. And of course won't cost us more money to provide more stock because of negative stock on order processing.

## Stack and Process

### Stack
Lumen PHP Framework is used for this POC because of it's the best framework for back-end programming I am currently best at and it provides **queue** for handling such case. This POC uses MySQL as the DBMS. All the request and response payload is JSON formatted. The response code is HTTP response status code (not in the response payload). Here is the schema of order processing with the involved technologies.

![Stacks and the process](http://darkrai.hereis.my.id/order-service-schema.jpg)

The cloud messaging service and the publish operation is **not included** in the repository. But I have provided the space where to put publish operations when handling success or failed order processing. It's in the `app\Jobs\PayOrderJob` class.

### The Process
1. The process starts with customers log in to the app
2. They add the same product to their cart as long as the stock is still available. 
3. At the nearly same time, they pay their cart to make order as described in the above picture with (let's say) a slightly different time between the orders. The app should 
4. The payment endpoint handler in Lumen API validate their orders, and check if they have something in their cart to be paid.
5. If they are clear, then Lumen API dispatch Order Processing Job the the Queue for every order requests.
6. After dispatching job, Lumen API will send 202 HTTP response for each requests as the order has been received but still waiting for the status. At this time, the app should show the waiting screen while waiting for the order status from Lumen Queue.
7. The Lumen Queue execute every single job with the respective order (based on how fast they enter the queue) and set the order success for the first two orders as the available stock is only 2 and set the rest as failed order.
8. Lumen Queue should push message to every customers' app about their orders' status. 

## Database Schema

Here is the database schema used in this POC. 

![Database ERD](http://darkrai.hereis.my.id/order-service-tables.png)

And because I use database as driver of the Lumen Queue, here are the tables to store the queue.

![enter image description here](http://darkrai.hereis.my.id/order-service-jobs.png)


## Installation

You can install the POC by cloning this repo and execute this command to start the installation.

```
$ composer install
```

After the installation finished, simply copy `.env.example` file to `.env`. Create database and set the database configuration in the `.env` file. Also set the app key and app URL for the API.

```
APP_KEY=
...
APP_URL=
...
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=root
DB_PASSWORD=
```

After setting `.env` file, set the JWT secret for the authentication

```
$ php artisan jwt:secret
```

Finally, migrate and seed the database.

```
$ php artisan migrate --seed
```

If you want to run on your local, use this command.

```
$ php -S [host]:[port] -t public
```

## API Documentation

The API can be accessed publicly on `https://ordersvc.hereis.my.id` and the documentation can be accessed on this URL:
https://documenter.getpostman.com/view/571767/Tz5wVDzR

## Logging

The API logs are stored at `storage/logs/api-[y-m-d].log` and rotated daily using Lumen log rotator. It logs every request (url, route name and payload of course) and response (payload and HTTP status code) also the timestamp of the logs. I put the logging codes in the `App\Http\Middleware\LogMiddleware`. Every single requests are given id with UUID format for tracking purpose and using `DEBUG` tag.

## Error Handling

Every errors occurred on this POC will logged in the same file with the api logging but with `ERROR` tag. The log contains the request ID to identify which API request make the error occurred even though they are on the same log file.

## Testing

For testing purpose, `phpunit` is being used. To start testing, do the installation described in the [Installation](#installation) section but stop until generating JWT Secret step and don't migrate and seed the database as the test case I provide will do the job on the test initiation.

```
$ ./vendor/bin/phpunit
```
