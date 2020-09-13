# How to add JWT Authentication to a CodeIgniter 3 REST API


- https://gfx-jwt-api.herokuapp.com/get-token
- https://gfx-jwt-api.herokuapp.com/verify-token
- *Resource* : Tutorials (http://developer-city.com/jwt)
- *Resource* : Verify Signature (https://jwt.io/)
- *Files* : `Libraries/CreatorJwt.php` & `libraries/JWT.php`
- `welcome/LoginToken`
- Response

{
    "Token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1bmlxdWVJZCI6IjExIiwicm9sZSI6ImFsYW1naXIiLCJ0aW1lU3RhbXAiOiIyMDIwLTA3LTIzIDA3OjEwOjQ1In0.buTC0d5F1YGgnLl3J-i1mCNqruUMZ6RA2YYOfqDa1No"
}

- `welcome/GetTokenData`
- Pass `Authorization` with value `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1bmlxdWVJZCI6IjExIiwicm9sZSI6ImFsYW1naXIiLCJ0aW1lU3RhbXAiOiIyMDIwLTA3LTIzIDA3OjEwOjQ1In0.buTC0d5F1YGgnLl3J-i1mCNqruUMZ6RA2YYOfqDa1No`
- Response

{
    "response":{
        "uniqueId": "11",
        "role": "alamgir",
        "timeStamp": "2020-07-23 04:25:47"
    },
    "id": 91
} 

-------

# JWT with Mobile App

- Send data to server to create JWT token : `<url-for-project-files>/get-token`
- Send data to server with Authorization to decode JWT token : `<url-for-project-files>/verify-token`

----

### Using Public / Private key

- Generate a pair of keys here : http://travistidwell.com/jsencrypt/demo/ - **GUI Version**
- Use command (http://travistidwell.com/jsencrypt/)
- cmd : `openssl genrsa -out private.pem 1024` where *private.pem* is the private key file name
- cmd : `openssl rsa -pubout -in private.pem -out public.pem` get public key from private key
