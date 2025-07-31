function uploadDocument(formElement) {
    const formData = new FormData(formElement);

    // Afficher progression
    Swal.fire({
        title: 'Téléversement en cours',
        html: 'Veuillez patienter...',
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('/admin/documents/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                html: `
                    ${data.message}<br>
                    <small class="text-muted">
                        Fichier: ${data.file_info.name}<br>
                        Taille: ${data.file_info.size}<br>
                        Type: ${data.file_info.type}
                    </small>
                `
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Erreur de téléversement',
            text: error.message || 'Une erreur est survenue lors du téléversement',
            footer: '<small>Si le problème persiste, contactez le support technique</small>'
        });
    });
}
