# Load Push: Http

Put any valid URL as your `uri`

You can find all the options available here:
[Symfony Http Client](https://symfony.com/doc/current/http_client.html)

<!-- config starts -->
```yaml
options:
    method:               POST

    # array containing the username as first value, and optionally the password as the second one
    auth_basic:           null

    # a token enabling HTTP Bearer authorization
    auth_bearer:          null

    # array of header names and values: "X-My-Header: My-Value"
    headers:              []

```
<!-- config ends -->
