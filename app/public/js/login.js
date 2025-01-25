document.querySelector('#login-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const email = document.querySelector('#login-username').value;
    const password = document.querySelector('#login-password').value;

    const loginParams = {
        method: 'POST', // Méthode POST
        headers: {
            'Content-Type': 'application/ld+json', // Type de contenu
        },
        body: JSON.stringify({ email, password }), // Données envoyées au serveur
        credentials: 'include' // Ajoutez cela si vous envoyez des cookies
    };

    try {
        const response = await fetch('/api/login', loginParams);
        const data = await response.json();

        if (response.ok) {
            // Connexion réussie, afficher un message de succès dans la console
            console.log('Connexion réussie:', data);

            // Redirection automatique basée sur le serveur (commentée)
            // if (data.redirect) {
            //     window.location.href = data.redirect; 
            // }

            // Redirection fixe vers dashboard.html
            window.location.href = 'dashboard.html';
        } else {
            // Afficher un message d'erreur
            console.error('Erreur de connexion:', data);
            alert(data.message || 'Erreur de connexion');
        }
    } catch (error) {
        console.error('Erreur réseau:', error);
        alert('Erreur réseau, veuillez réessayer plus tard.');
    }
});
