# Guestbook

Symfony powered application to add guestbooks by user. It uses Symfony 6.0.10 with PHP version 8.0.21.

## Installation

Use the repository link [git](https://github.com/irahulvarma/Guestbook.git) to clone the application.

```bash
git clone https://github.com/irahulvarma/Guestbook.git
```

Create a database in local MySQL server.

Navigate to the Guestbook directory, update the yourdbname with the newly created db name, also make sure to update username and password as per your setup in ```.env``` file.
```
DATABASE_URL="mysql://username:password@127.0.0.1:3306/yourdbname?serverVersion=5.7&charset=utf8mb4"
```

Run the following commands in the same order:
```
composer require runtime
php bin/console doctrine:migrations:migrate
npm install
npm run build
symfony server:start
```

After the application is started, register the first user and alter the role of that user by making it admin by running the following mysql alter query:
```
UPDATE `user` SET `roles` = '[\"ROLE_ADMIN\"]' WHERE (`id` = '1');
```
Your Admin user is ready now!

## Funtionalities
Any user can register and add guestbook. 

Admin can view,edit, approve and delete the records, also admin can see the list.

In homepage, it list the approved guestbook records.


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[GPL-3.0 license](https://github.com/irahulvarma/Guestbook/blob/main/LICENSE)