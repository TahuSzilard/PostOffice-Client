function openEditor(id, name) {
    document.getElementById('id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('editor-row').classList.remove('hidden');
}

function closeEditor() {
    document.getElementById('editor-row').classList.add('hidden');
    document.getElementById('id').value = '';
    document.getElementById('name').value = '';
}

function saveChanges() {
    const id = document.getElementById('id').value;
    const name = document.getElementById('name').value;
    
    // Itt AJAX hívást is használhatsz, ha az az API támogatja, például fetch API-val
    // fetch('http://localhost:8000/counties/' + id, { ... });
    
    alert("Mentett adatok: " + id + " - " + name);
    
    // Például elküldheted egy rejtett form segítségével
    document.getElementById('editor-row').classList.add('hidden');
}
