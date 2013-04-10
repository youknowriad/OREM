Rizeway OREM
============

[![Build Status](https://secure.travis-ci.org/youknowriad/OREM.png?branch=master)](http://travis-ci.org/youknowriad/OREM)

Rizeway OREM is a Restful API Abstraction Layer. It is to Restful APIs what doctrine is for databases.

Getting Started
---------------

Say you have the following JSON HTTP API to make CRUD on an object named "status" 

```
GET /status # Return a list of statuses
GET /status/1 # Return the status of id 1
POST /status # Create a status (the body of the request contain the hash of the status)
PUT /status/1 # Update the status of id 1 (the body of the request contain the hash of the status)
DELETE /status/1 # Delete the status of id 1
```

1- Create a folder to store your mappings.

2- Create a mapping file in this folder. The mapping file sould be named status.orem.yml and will look like this.

```yaml
class: MyNamespace/Status
fields:
    id:
        primary_key: true
    message:
        type: string
    author:
        type: string
    count_likes
        type: integer    
```

3- Create a simple entity class 

```php
<?php

namespace MyNamespace 

class Status 
{
    protected $id;
    protected $message;
    protected $author;
    protected $count_likes = 0;

    public function getId()
    {
        return $this->id;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getCountLikes()
    {
        return $this->count_likes;
    }

    public function addLike()
    {
        $this->count_likes++;
    }
}
```

4- Get an OREM Manager

```php
$factory = new \Rizeway\OREM\Config\Factory($directory, $apiBaseUrl);
$manager = $factory->getManager();
```

5- How to use the manager to make api calls

```php
$status = new \MyNamespace\Status();
$status->setMessage('my message');
$status->setAuthor('author');

$manager->persist($status); // Call POST API

$status->addLike();
$manager->update($status); // Call PUT API

$manager->remove($status); // Call DELETE API

$statuses = $manager->getRepository('status')->findAll(); // Call GET api and return an array of \MyNamespace\Status

$status = $manager->getRepository('status')->find(1); // GET api with primary key, return an object \MyNamespace\Status
```

Installation
------------
Install using composer 

```
{
    "require": {
        "rizeway/orem": "*"
    }
}
```

Roadmap
-------

 - Find Query Handling (GET with Url parameters)
 - Url Customisation
 - Entity Relations Mapping (HasMany, HasOne)
 - Custom api functions
 - More Field Types
 - Extra parameters in URL (like CAS ticket or Other auth token)

Contribute
----------
Install the dependancies using composer and your ready to go

```
git clone https://github.com/youknowriad/OREM.git && cd OREM
curl -s http://getcomposer.org/installer | php
./composer.phar install --dev
```

Tests
-----
OREM is tested using atoum 

```sh
./bin/atoum --test-all
```
