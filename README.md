# Ekasi Marketplace

## What it is
C2C marketplace - for local seller/consumers to be able to sell and buy products.Allows users to add list, sell, buy products. 

## Tech Stack
- PHP
- MySQL
- Bootstrap
- HTML/CSS

## Status
Currently live, hosted with infinity free

## How to run it locally
Run with XAMPP


## What I built
Short descriptive overview of one of the many codes have built the edit listings function, how it functions:

Starts a session, connects to the DB
Reads the product ID from the URL, dies if missing
Uses a prepared statement to safely fetch that product (protects against SQL injection)
Dies if no product matches
Renders a form pre-filled with the product's current name, price, stock, description, and image
Lets the seller optionally upload a new image
On submit, POSTs everything to update_listing.php
