document.addEventListener('contextmenu', function (e) {
    e.preventDefault(); // Désactive le clic droit
});

document.addEventListener('keydown', function (e) {
    // Désactive Ctrl+U, Ctrl+Shift+I, F12, etc.
    if (e.ctrlKey && e.key === 'u' || e.ctrlKey && e.key === 'U' || e.key === 'F12' || e.ctrlKey && e.shiftKey && e.key === 'I') {
        e.preventDefault();
        alert("L'accès au code source est désactivé.");
    }
});