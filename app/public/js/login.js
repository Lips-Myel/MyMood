const loginForm = document.querySelector('#login-form');

loginForm.addEventListener('submit', event => {
    event.preventDefault();

    const loginEmail = document.querySelector('#login-username').value;
    const loginPassword = document.querySelector('#login-password').value;

    authentificate(loginEmail, loginPassword);
});

function authentificate(email, password) {
    console.log(email);
    console.log(password);

    const loginParams = {
        method: 'POST', // Méthode POST
        headers: {
            'Content-Type': 'application/json', // Type de contenu
        },
        body: JSON.stringify({ email, password  }), // Données envoyées au serveur
        credentials: 'include'  // Ajoutez cela si vous envoyez des cookies
    };

    const apiUrl = 'https://localhost:443/api/login';
    fetch(apiUrl, loginParams)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Réponse du serveur :', data);
            // Ajoutez ici la logique pour gérer la réponse, par exemple la redirection
        })
        .catch(error => console.error('Erreur :', error));
}
