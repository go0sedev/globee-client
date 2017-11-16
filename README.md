# GloBee PHP Client
This is a lightweight library to integrate your website with your GloBee account and accept crypto payments on 
your website.

## Server Requirements
The following packages need to be installed on the server:
```bash
php 7.1
php7.1-bcmath
```

## Installation with Composer
Run the following command in your project to add this package:
```bash
composer require gustavtrenwith/globee_client
```
Then run `composer update`.

## Environment Setup
You need to add the following to your .env file. Then you can easily disable the exception emails by changing the 
variable value to true.
```
ECDSA_SIN=
ECDSA_PRIVATE_KEY_HEX=
ECDSA_PUBLIC_KEY_COMPRESSED=
```

## Generate ECDSA Keypair Values
You need to use the following code to create the ECDSA Keypair Values. Once done, this code can safely be removed.
```php
GloBeeClient::GenerateECDSAKeys();
```
An associative array will be returned upon success:
```php
Array
(
  [private_key_hex] => 7a4fbece43963538cb8f9149b094906168d71be36cfb405e6930fddb42da2c7d
  [private_key_dec] => 55323065337948610870652254548527896513063178460294714145329611159...
  [public_key] => 043fbbf44c3da3fec12bf7bac254fd176adc3eaed79470932b574d8d60728eb206fb7a...
  [public_key_compressed] => 033fbbf44c3da3fec12bf7bac254fd176adc3eaed79470932b574d8d607...
  [public_key_x] => 3fbbf44c3da3fec12bf7bac254fd176adc3eaed79470932b574d8d60728eb206
  [public_key_y] => fb7ac7ac6959f75a6859a1a8d745db7e825a3c5c826e5b2e4950892b35772313
)
```

## Feedback
For any questions or suggestions, feel free to contact me on `gtrenwith@gmail.com`
