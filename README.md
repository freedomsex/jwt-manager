

```
 mkdir -P /config/keys/
 $projectDir.'/config/keys/private.key'
```

# Keys Example

```
 openssl genrsa -out private.key 1024
 openssl rsa -in private.key -pubout -outform PEM -out public.key
```

### private.key
### public.key