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

- Install mkcert from here https://github.com/FiloSottile/mkcert
- Install the certificate for development like so `mkcert -install`
- Navigate to the certs directory in the containers directory of this project and generate the projects certs `mkcert -cert-file crt.crt -key-file key.key na.test www.na.test` 
- Create a `.env` file based on the `.env.dist` file
- Change your `SSH_DIR` to suit your computer and the database env vars to suit a database you expect to create in mysql once runniing
- Run `./app start`
- Run `./app terminal php` to get a command line in the php container
- In the php container run `keys && composer install`, then exit to the host
- Run `./app terminal node` to get a command line in the node container
- In the node container run `npm install`, then exit to the host
- Open your favourite mysql client and connect to the mysql container
- Create a database with the details you put in the .env before
- Run `./app terminal php` to get a command line in the php container
- In the php container run `./cli migrations:migrate && ./cli db:seed`, then exit to the host
- Now point your primary dns in your computer to `127.0.0.1` and your secondary dns to a public dns server
- In your browser visit `na.test`