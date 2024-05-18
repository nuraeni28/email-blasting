## Step Running The Email Blasting System

The Email Blasting System using Laravel.

- Run the command on the terminal
```bash
$ php artisan migrate
```
```bash
$ php artisan serve
```

- Run the command on the other terminal to running the queue
```bash
$ php artisan queue:work
```

## Send Blasting With API 

```http
POST /api/blast-message
```
- Body
Example :
```javascript
[
    {
        "message": "example message with low priority",
        "email": ["xxxx@gmail.com"],
        "priority": "low"
    },
     {
        "message": "example message with high priority",
        "email": ["yyy@gmail.com"],
        "priority": "high"
    }
]

```

- Responses
```javascript
{
  "message" : string,
  "success" : bool,
  "data"    : array
}
```


