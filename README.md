# Vulnerable Book Store Application
VulnyBooks is a deliberately insecure web application that mimics a basic BOOK STORE built using Laravel (API) to demonstrate and teach the OWASP Top 10 vulnerabilities in a practical, API-driven environment.

**Caution !! The application is a basic implementation of a book store and should not be used in production**

## Features & Vulnerabilities

**Broken Authentication**
- Plaintext Passwords	Passwords are stored without encryption, making data breaches worse.
- Weak JWT Secret	Tokens can be forged using tools due to predictable secrets.
- No Token Expiry	A stolen token grants indefinite access.
- Missing Password Hashing	Bypasses Laravel’s native hashing mechanisms.
- No Token Invalidation	Tokens stay valid even after password changes.
- No Ownership Validation	Any user with a valid token can modify other users' data.

**JWT & Role-Based Vulnerabilities**
- Role via JWT Claim	Users can self-assign roles (e.g., admin) within JWT.
- No DB Role Verification	JWT claims aren’t cross-verified against the database.
- Hardcoded Secret	JWT secret is hardcoded, enabling attackers to forge tokens.
- No Client-Side Signature Validation	Client accepts any JWT signed with the secret.

**Authorization & Input Flaws**
- IDOR (Insecure Direct Object Reference)	Users can update or access others’ data by changing IDs.
- Missing Authorization Checks	No server-side check to confirm resource ownership.
- Lack of Input Validation	Accepts malformed emails, passwords, and other inputs.
- Use  of Plaintext Passwords	Passwords remain unencrypted in the database.

**Unbounded File Uploads ( RCE )**
- No file size limit: Users can upload massive PDFs or arbitrary files.
- No MIME type validation: Uploads are accepted as-is — .exe, .php, .zip, etc.
- No storage quota: A single user can fill up disk space.

**Insecure Deserialization**
- Allow users to upload or send serialized objects (PHP or JSON) that get unserialize()d on the server.

**Mass Assignment**
- Allow clients to assign fields like approved, user_id, role, etc., when creating or updating models.

**Injection**
- SQLi in search, Stored XSS in review tables

**Software and Data Integrity Failures**
- JWTs can be tampered with and are trusted without verification.
- No integrity checks on uploaded files (webshells possible).

**Insecure Design**
- Core logic trusts client input (e.g., token role, user ID).
- Reviews and books auto-approve based on claims.
- File upload accepts any content type.


## Installation and setup (Local Installation)

### Requirements
- php -v 8.3.*.*
- composer
- git

1. Clone the repository:

``` bash
  git clone https://github.com/mayosmjs/vulnibook.git

  cd vulnibook

  touch database/database.sqlite

  composer install

```

2. Scaffolding

``` bash
  php artisan migrate --seed

```

3. Start the application
``` bash
   php run serve
```



## Walk Through
COMING SOON!



# 🛡 Legal Disclaimer
This software is provided for educational and ethical testing purposes only. The authors are not responsible for any misuse or damage caused by deploying this code in unsafe environments.
