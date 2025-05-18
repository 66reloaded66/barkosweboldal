// createUser.js
const fs = require('fs');
const bcrypt = require('bcrypt');

const username = 'admin'; // ← ide írd be a felhasználónevet
const password = 'titkos123'; // ← ide a jelszót

bcrypt.hash(password, 10, (err, hash) => {
  if (err) throw err;

  const userData = [
    {
      username,
      password: hash
    }
  ];

  fs.writeFileSync('users.json', JSON.stringify(userData, null, 2));
  console.log('Felhasználó létrehozva: ', username);
});