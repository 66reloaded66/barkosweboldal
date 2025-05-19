const express = require('express');
const fs = require('fs');
const path = require('path');
const multer = require('multer');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const PORT = 3000;

// Static mappák
app.use(cors());
app.use(express.static(path.join(__dirname, 'public')));
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));
app.use(bodyParser.json());

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

// ✅ Képek lekérdezése kategória szerint
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

app.listen(PORT, () => {
  console.log(`Admin szerver fut a http://localhost:${PORT}/ címen`);
});
