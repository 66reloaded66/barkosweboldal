const dropZone = document.getElementById("dropZone");
const fileInput = document.getElementById("fileInput");
const uploadList = document.getElementById("uploadList");
const saveBtn = document.getElementById("saveBtn");
const message = document.getElementById("message");

let filesData = []; // tömbben lesz az összes fájl és annak metaadata

// Drag & drop események
dropZone.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropZone.classList.add("dragover");
});

dropZone.addEventListener("dragleave", () => {
  dropZone.classList.remove("dragover");
});

dropZone.addEventListener("drop", (e) => {
  e.preventDefault();
  dropZone.classList.remove("dragover");

  const droppedFiles = [...e.dataTransfer.files];
  addFiles(droppedFiles);
});

fileInput.addEventListener("change", (e) => {
  addFiles([...e.target.files]);
});

function addFiles(newFiles) {
  // Csak képfájlokat engedünk
  newFiles = newFiles.filter(f => f.type.startsWith("image/"));

  newFiles.forEach(file => {
    // Minden fájlhoz egy alap adat objektumot
    filesData.push({
      file,
      title: "",
      type: null,
      description: "",
      visible: true,
      progress: 0
    });
  });

  renderUploadList();
  saveBtn.disabled = filesData.length === 0;
}

function renderUploadList() {
  uploadList.innerHTML = "";
  filesData.forEach((fileObj, index) => {
    // Kép előnézet
    const imgURL = URL.createObjectURL(fileObj.file);

    // Tartalom
    const item = document.createElement("div");
    item.className = "upload-item";

    item.innerHTML = `
      <img src="${imgURL}" alt="Kép előnézet" />
      <div class="upload-details">
        <div class="input-group">
          <label>Cím:</label>
          <input type="text" data-index="${index}" class="title-input" placeholder="Alkotás címe" value="${fileObj.title}" />
        </div>

        <div class="input-group">
          <label>Típus:</label>
          <div class="type-buttons" data-index="${index}">
            <button type="button" class="type-button" data-type="zomanc">Zománc</button>
            <button type="button" class="type-button" data-type="festmeny">Festmény</button>
            <button type="button" class="type-button" data-type="vallasi">Vallási</button>
            <button type="button" class="type-button" data-type="ekszer">Ékszer</button>
          </div>
        </div>

        <div class="input-group">
          <label>Leírás:</label>
          <textarea data-index="${index}" class="description-input" placeholder="Alkotás leírása">${fileObj.description}</textarea>
        </div>

        <label class="visibility-checkbox">
          <input type="checkbox" data-index="${index}" class="visible-checkbox" ${fileObj.visible ? "checked" : ""} /> Láthatóság
        </label>

        <div class="upload-progress"><div style="width: ${fileObj.progress}%"></div></div>
      </div>
    `;

    uploadList.appendChild(item);
  });

  addInputListeners();
  addTypeButtonListeners();
}

function addInputListeners() {
  document.querySelectorAll(".title-input").forEach(input => {
    input.oninput = (e) => {
      const idx = e.target.dataset.index;
      filesData[idx].title = e.target.value;
    };
  });

  document.querySelectorAll(".description-input").forEach(textarea => {
    textarea.oninput = (e) => {
      const idx = e.target.dataset.index;
      filesData[idx].description = e.target.value;
    };
  });

  document.querySelectorAll(".visible-checkbox").forEach(chk => {
    chk.onchange = (e) => {
      const idx = e.target.dataset.index;
      filesData[idx].visible = e.target.checked;
    };
  });
}

function addTypeButtonListeners() {
  document.querySelectorAll(".type-buttons").forEach(container => {
    const idx = container.dataset.index;
    container.querySelectorAll("button").forEach(btn => {
      btn.onclick = () => {
        filesData[idx].type = btn.dataset.type;
        // Egy kiválasztás csak egy lehet:
        container.querySelectorAll("button").forEach(b => b.classList.remove("selected"));
        btn.classList.add("selected");
      };
    });
  });
}

document.getElementById("uploadForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  if(filesData.length === 0) return;

  saveBtn.disabled = true;
  showMessage("", true); // először üres

  try {
    for(let i=0; i < filesData.length; i++) {
      await simulateUpload(filesData[i], i);
    }

    await saveMetadata();

    showMessage("Mentés sikeres! Átirányítás az admin oldalra...", true);
    setTimeout(() => {
      window.location.href = "admin.html";
    }, 2000);

  } catch(err) {
    showMessage("Hiba történt a mentés során.", false);
    saveBtn.disabled = false;
  }
});

// Feltöltés szimuláció (progress + idő)
function simulateUpload(fileObj, index) {
  return new Promise((res) => {
    let progress = 0;
    const interval = setInterval(() => {
      progress += 10 + Math.random() * 20;
      if(progress >= 100) {
        progress = 100;
        clearInterval(interval);
        fileObj.progress = 100;
        updateProgress(index, 100);
        res();
      } else {
        fileObj.progress = progress;
        updateProgress(index, progress);
      }
    }, 300);
  });
}

function updateProgress(index, progress) {
  const progressBar = uploadList.querySelectorAll(".upload-progress > div")[index];
  if(progressBar) progressBar.style.width = progress + "%";
}

async function saveMetadata() {
  // Ez backendhez kell, itt csak console log:
  console.log("Mentett metaadatok:", filesData);
  // Itt POST fetch-elheted a JSON-t és fájlokat a szerverre.

  // Példa:
  /*
  await fetch('/upload', {
    method: 'POST',
    body: new FormData(document.getElementById('uploadForm'))
  });
  */
}

// Az üzenet megjelenítése, animációval
function showMessage(text, isSuccess = true) {
  message.textContent = text;
  if(isSuccess) {
    message.style.color = ""; // CSS-ben kezelve
    message.classList.add("visible");
  } else {
    message.style.color = "red";
    message.classList.remove("visible");
  }
}


