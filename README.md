# How to add JWT Authentication to a CodeIgniter 3 REST API

- https://gfx-jwt-api.herokuapp.com/index
- https://gfx-jwt-api.herokuapp.com/get-token
- https://gfx-jwt-api.herokuapp.com/verify-token
- *Resource* : Tutorials (http://developer-city.com/jwt)
- *Resource* : Verify Signature (https://jwt.io/)
- *Resource* : PHP Package PHP-JWT (https://firebaseopensource.com/projects/firebase/php-jwt/)

-------

# Public & Private Keys

- *Resource* : Tutorials (https://morioh.com/p/1e376919d0af)
- Generate a pair of keys here : http://travistidwell.com/jsencrypt/demo/ - **GUI Version**
- *Resource* : Console Key Generator (http://travistidwell.com/jsencrypt/)
- cmd : `openssl genrsa -out private.pem 1024` where *private.pem* is the private key file name
- cmd : `openssl rsa -pubout -in private.pem -out public.pem` get public key from private key
- *Files* : `keys/private.pem` & `keys/private.pem`

-------

# JWT with Mobile App

- Send Public Key to server to check if CLIENT is VALID : `<url-for-project-files>/index`
- Return Response if CLIENT is ALLOW or not.
- If CLIENT is ALLOW, send data to server to create JWT token : `<url-for-project-files>/get-token`
- If CLIENT is ALLOW, send JWT token to server to verify and decode JWT token : `<url-for-project-files>/verify-token`