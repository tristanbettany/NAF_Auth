```
███╗   ██╗     █████╗     ███████╗
████╗  ██║    ██╔══██╗    ██╔════╝
██╔██╗ ██║    ███████║    █████╗  
██║╚██╗██║    ██╔══██║    ██╔══╝  
██║ ╚████║    ██║  ██║    ██║     
╚═╝  ╚═══╝    ╚═╝  ╚═╝    ╚═╝
                                  
==================================
      Its Not A Framework
==================================
```

# NAF Auth

An implementation of a selection of authentication platforms built on the NAF system in PHP

## Usage

### Pre-requisits

- Clone the repo, and your on windows, change the line endings of the `cli` file in the src folder to LF
- If your using git bash for windows you may also want to change the line endings of the `app` command in the root to LF also  
- Install mkcert from here https://github.com/FiloSottile/mkcert
- Install the certificate for development like so `mkcert -install`
- Navigate to the certs directory in the containers directory of this project and generate the projects certs `mkcert -cert-file crt.crt -key-file key.key na.test www.na.test`
- Create a `.env` file based on the `.env.dist` file

### Okta Setup

- Sign up for a okta developers account at https://developer.okta.com/
- Sign in and go to the `Applications` area in the dashboard
- Click `Create App Integration`
- Select `SAML 2.0` and click next
- Type an App name and click next
- Set the Single Sign On URL as `https://na.test/login`
- Set the Audience URI as `https://na.test/login`
- Set Name ID Format as `Email Address`
- Leave the rest unchanged and move onto the attribute statements
- Add 4 attribute statements, `id`, `email`, `firstName`, `lastName` (you may need to map these in okta profile setup but give it a whirl first)
- Click next and create your app integration
- From here you will be given the app integration details, download the cert and put it in the src folder of the repo
- Add the login url to your .env file and assure your attribute statements match the .env file and the cert file name too

### Initial App Setup

- Change your `SSH_DIR` in the env file to suit your computer 
- Change the database env vars to suit a database you expect to create in mysql once the stack is running
- Run `./app start`
- Run `./app terminal php` to get a command line in the php container
- In the php container run `keys && composer install`, then exit to the host
- Run `./app terminal node` to get a command line in the node container
- In the node container run `npm install`, then exit to the host
- Open your favourite mysql client and connect to the mysql container
- Create a database with the details you put in the .env before
- Run `./app terminal php` to get a command line in the php container
- In the php container run `./cli migrations:migrate`, then exit to the host

### Ready to go

- Now change your hosts file to point `na.test` and `www.na.test` to `127.0.0.1` (If you don't have the ability to change your hosts file you may need to run a dns container or proxy container)
- You are now ready to visit the app in your browser at `na.test`
- You should be able to log in with okta from here
- If you want to log in with email and password, then seed a test user using the apps `db:seed` command from the php container
- The login for the seeded user would be `test@test.com` & `letmein`