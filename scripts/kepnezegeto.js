const posts = [
      {
        image: 'images/kep1.jpg', // A kép elérési útja
        text: 'Ez egy kép a műhelyből.', // A képhez tartozó szöveg
        visible: true // A kép látható-e
      },
      {
        image: 'images/kep2.jpg',
        text: 'Ez a második kép.',
        visible: false // Ez a kép nem jelenik meg, mert nem látható
      }
    ];

    const gallery = document.getElementById('gallery'); // Lekérjük a galéria divet

    // Végigmegyünk a posztokon és csak a láthatókat adjuk hozzá
    posts.forEach(post => {
      if (post.visible) { // Csak akkor, ha a poszt látható
        const div = document.createElement('div'); // Létrehozunk egy új divet
        div.className = 'post'; // Osztály hozzáadása a stílushoz

        const img = document.createElement('img'); // Kép elem létrehozása
        img.src = post.image; // Beállítjuk a kép forrását

        const text = document.createElement('div'); // Szöveghez div létrehozása
        text.className = 'post-text'; // Osztály a stílus miatt
        text.innerText = post.text; // Beállítjuk a szöveget

        div.appendChild(img); // Kép hozzáadása a poszt divhez
        div.appendChild(text); // Szöveg hozzáadása a poszt divhez
        gallery.appendChild(div); // A teljes poszt hozzáadása a galéria divhez
      }
    });