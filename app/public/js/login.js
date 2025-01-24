const loginForm = document.querySelector('#login-form');

loginForm.addEventListener('submit', event => {
    event.preventDefault();

    const loginUsername = document.querySelector('#login-username').value;
    const loginPassword = document.querySelector('#login-password').value;

    authentificate(loginUsername, loginPassword);
});

function authentificate(username, password) {
    console.log(username);
    console.log(password);

    const loginParams = {
        method: 'POST', // Méthode POST
        headers: {
            'Content-Type': 'application/json', // Type de contenu
        },
        body: JSON.stringify({ username, password }), // Données envoyées au serveur
        credentials: 'include'  // Ajoutez cela si vous envoyez des cookies
    };

    const apiUrl = 'http://localhost/api/login';
    fetch(apiUrl, loginParams)
        .then(response => {
            if (!response.ok) {
                throw new Error(HTTP `error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => console.log('Réponse du serveur :', data))
        .catch(error => console.error('Erreur :', error));
}