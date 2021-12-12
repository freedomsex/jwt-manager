
## JWT Manager

> for FreedomSex projects
> 
> for you without a guarantee of version compatibility, and everything else
 
Firebase\JWT is used internally

You need to create keys for signing the token. 

```
 mkdir -P /config/keys/ 
 cd config/keys 
```
## Keys Example

```
 openssl genrsa -out private.key 1024
 openssl rsa -in private.key -pubout -outform PEM -out public.key
```

Specify the paths when creating an instance of the manager. And _TimeToLive_ generated token as the last argument

```php
$manager = new JWTManager(
    '../keys/private.key',
    '../keys/public.key',
    $this->ttl
);
```

## Generate JWT token

Simple JWT token. Only Expires in payload

```php
$token = $manager->create();
``` 
> eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJleHAiOjE2Mzg1NDkwMTN9.GfefYgNnxpUCU_n8t0d37tQP-pjP7euGhrHTDx4T3ta0Eaa5Bedved5KbzZF-yXMUstnXr3TVRu3dkbKCaf0h2OJp13LT1WgvsyrkMIeO2KRG-vwsFrGrzAHRu2O5OKa7WI3sIFDE-oc_khyPFvO01UdiLtpEISOh8ys3Dh32-8 

**HEADER**
```json
{
  "typ": "JWT",
  "alg": "RS256"
}
```
**PAYLOAD** 
```json
{
  "exp": 1638549013
}
```

https://jwt.io

### Payload based on User instance

```php
$token = $manager->create($user);
// getId - uid
// getRoles - roles
// getIdentityId - uuid
// getAccess - access
// getSubject -sub
```

#### Deprecations

`uuid` - deprecated since 0.4: use `uid` and `id` 

#### Expires "alternative" way
```php
$token = $manager->create($user, 1638549013); 
```

## Load payload
```php
$payload = $manager->load($token); // object return
$payload = (array) $manager->load($token); // array return
```
```php
[
  "uid" => 1
  "exp" => 1638549013
]
```

### Inheritance and Overriding

Override method `populatePayload` to Define your own structure

```php
public function populatePayload(array $payload, $user = null): array
```
