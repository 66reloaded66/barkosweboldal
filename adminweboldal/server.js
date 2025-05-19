const express = require('express');
const fs = require('fs');
const path = require('path');
const multer = require('multer');
const bodyParser = require('body-parser');
const cors = require('cors');
const bcrypt = require('bcrypt'); // bcrypt importálása

const app = express();
const PORT = 3000;

// Static mappák
app.use(cors());
app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));
app.use(bodyParser.json());

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Multer beállítások – képek mentése
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, 'uploads/');
  },
  filename: function (req, file, cb) {
    const unique = Date.now() + '-' + file.originalname;
    cb(null, unique);
  }
});
const upload = multer({ storage: storage });

// JSON fájl elérési útvonala
const POSTS_JSON = path.join(__dirname, 'data', 'posts.json');

// Helper: JSON fájl olvasása
function readPosts() {
  if (!fs.existsSync(POSTS_JSON)) return [];
  return JSON.parse(fs.readFileSync(POSTS_JSON));
}

// Helper: JSON fájl mentése
function savePosts(posts) {
  fs.writeFileSync(POSTS_JSON, JSON.stringify(posts, null, 2));
}

const USERS_JSON = path.join(__dirname, 'users.json');

function readUsers() {
  if (!fs.existsSync(USERS_JSON)) return [];
  return JSON.parse(fs.readFileSync(USERS_JSON));
}

// Bejelentkezés kezelése bcrypt-tel
app.post('/login', (req, res) => {
  const { username, password } = req.body;
  const users = readUsers();

  const user = users.find(u => u.username === username);
  if (!user) {
    return res.status(401).json({ success: false, message: 'Hibás felhasználónév vagy jelszó' });
  }

  // bcrypt összehasonlítás, mert user.password hash
  bcrypt.compare(password, user.password, (err, result) => {
    if (err) {
      return res.status(500).json({ success: false, message: 'Szerverhiba történt' });
    }
    if (!result) {
      return res.status(401).json({ success: false, message: 'Hibás felhasználónév vagy jelszó' });
    }
    // Sikeres bejelentkezés
    res.json({ success: true });
  });
});

// --- A többi végpont változatlan ---

// ✅ Kép feltöltése és metaadatok mentése
app.post('/api/upload', upload.single('image'), (req, res) => {
  const { category, description, visible } = req.body;
  const posts = readPosts();

  const newPost = {
    id: Date.now().toString(),
    filename: req.file.filename,
    category,
    description,
    visible: visible === 'true'
  };

  posts.push(newPost);
  savePosts(posts);

  res.json({ success: true, post: newPost });
});

// ✅ Képek lekérdezése kategória szerint (csak látható képek)
app.get('/api/posts/:category', (req, res) => {
  const { category } = req.params;
  const posts = readPosts();
  const filtered = posts.filter(p => p.category === category && p.visible);
  res.json(filtered);
});

// ✅ Láthatóság módosítása (admin toggle)
app.post('/api/toggle/:id', (req, res) => {
  const { id } = req.params;
  const posts = readPosts();
  const index = posts.findIndex(p => p.id === id);
  if (index === -1) return res.status(404).json({ error: 'Post not found' });

  posts[index].visible = !posts[index].visible;
  savePosts(posts);
  res.json({ success: true, visible: posts[index].visible });
});

// ✅ Admin: összes kép lekérése (láthatóságtól függetlenül)
app.get('/api/admin/posts', (req, res) => {
  const posts = readPosts();
  res.json(posts);
});

// ✅ Kép adatainak frissítése
app.put('/api/posts/:id', (req, res) => {
  const { id } = req.params;
  const { category, description, visible } = req.body;
  const posts = readPosts();
  const index = posts.findIndex(p => p.id === id);
  if (index === -1) return res.status(404).json({ error: 'Post not found' });

  posts[index] = {
    ...posts[index],
    category: category || posts[index].category,
    description: description || posts[index].description,
    visible: typeof visible === 'boolean' ? visible : posts[index].visible
  };

  savePosts(posts);
  res.json({ success: true, post: posts[index] });
});

// ✅ Kép törlése
app.delete('/api/posts/:id', (req, res) => {
  const { id } = req.params;
  let posts = readPosts();
  const index = posts.findIndex(p => p.id === id);
  if (index === -1) return res.status(404).json({ error: 'Post not found' });

  const [deletedPost] = posts.splice(index, 1);
  savePosts(posts);

  // Töröljük a képfájlt is
  const filePath = path.join(__dirname, 'uploads', deletedPost.filename);
  fs.unlink(filePath, err => {
    if (err) console.error('Hiba a fájl törlésekor:', err);
  });

  res.json({ success: true });
});

// ✅ Nyilvános nézegető oldalnak: látható képek lekérése kategória szerint
app.get('/api/gallery/:category', (req, res) => {
  const { category } = req.params;
  const posts = readPosts();
  const filtered = posts.filter(
    (p) => p.category === category && p.visible === true
  );
  res.json(filtered);
});

app.listen(PORT, () => {
  console.log(`Admin szerver fut a http://localhost:${PORT}/ címen`);
});
