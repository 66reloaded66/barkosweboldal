const express = require("express"); // Express web framework
const session = require("express-session"); // Session kezelés
const bcrypt = require("bcrypt"); // Jelszó hash-elés
const path = require("path");
const app = express();
const PORT = 3000;

// 🔐 Képzelt felhasználó (adatbázis helyett)
const users = [
  {
    username: "tejfoloskenyer",
    passwordHash: "$2b$10$GpHLih3jF19GvR6EpSYWTugjGH6ArXFxhIQYNRe/1BmcE9eMI3ABK" // Jelszó: titok123
  }
];

// 🌐 Middleware beállítások
app.use(express.urlencoded({ extended: true })); // POST adatok olvasása
app.use(express.static(path.join(__dirname, "public"))); // statikus fájlok kiszolgálása

// 💾 Session konfiguráció
app.use(
  session({
    secret: "nagyontitkoskulcs", // Titkos kulcs
    resave: false,
    saveUninitialized: false,
    cookie: { secure: false } // HTTPS esetén legyen true
  })
);

// 🟢 GET / - login oldal
app.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, "public", "index.html"));
});

// 🔐 POST /login - bejelentkezés kezelése
app.post("/login", async (req, res) => {
  const { username, password } = req.body;
  const user = users.find(u => u.username === username);

  if (!user) return res.send("Hibás felhasználónév vagy jelszó");

  const match = await bcrypt.compare(password, user.passwordHash);

  if (match) {
    req.session.user = username;
    res.redirect("/dashboard");
  } else {
    res.send("Hibás felhasználónév vagy jelszó");
  }
});

// 🛡️ Middleware: csak belépett felhasználónak engedélyez
function authMiddleware(req, res, next) {
  if (req.session.user) {
    next();
  } else {
    res.redirect("/");
  }
}

// 📋 GET /dashboard - csak belépve elérhető
app.get("/dashboard", authMiddleware, (req, res) => {
  res.send(`<h1>Üdv, ${req.session.user}!</h1><p><a href="/logout">Kijelentkezés</a></p>`);
});

// 🚪 GET /logout - kijelentkezés
app.get("/logout", (req, res) => {
  req.session.destroy(() => {
    res.redirect("/");
  });
});

// 🚀 Szerver indítása
app.listen(PORT, () => {
  console.log(`Szerver fut: http://localhost:${PORT}`);
});
