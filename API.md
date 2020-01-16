# Introduction

By defining constants in the db.php file, it is easier to keep the code at minimum and reduce the amout of duplicated methods needed.
This will make it easier to switch between different database software, if the syntax is interperated the same way.

General about responses
The api responds with two root values for each and every request
The first root value is the status of the request, if it was processed correctly it will retur true, otherwise it will return false
The second root value is the message from the api, depending on the request it will either contain error message, message to the user or be empty.
Example:
```JSON
{
    "status":true,
    "message":""
}
```

Response with data
```JSON
{
    "status":true,
    "message":"",
    "data":[

    ]
}
```

## Connections

The API will reject non-secure attempted connections.
Calls using regular http will result in a output in json-format 