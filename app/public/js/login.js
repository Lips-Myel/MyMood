document.querySelector('#login-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const email = document.querySelector('#login-username').value;
    const password = document.querySelector('#login-password').value;

    // Validation des champs
    if (!email || !password) {
        alert('Veuillez entrer une adresse e-mail et un mot de passe.');
        return;
    }

    const loginParams = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
        credentials: 'include',
    };

    try {
        const response = await fetch('/api/login', loginParams);
    
        if (!response.ok) {
            console.error('Erreur serveur:', response.status);
            alert('Erreur lors de la connexion au serveur.');
            return;
        }
    
        const data = await response.json();
        console.log('Réponse:', data);

        //Redirection selon le rôle utilisateur
        const userRoles = data.user.roles;
        if (userRoles.includes('Administrateur')) {
            window.location.href = '/admin/admin_dashboard.html';
        } else if (userRoles.includes('Superviseur')) {
            window.location.href = '/supervisor/supervisor_dashboard.html';
        } else if (userRoles.includes('Étudiant')) {
            window.location.href = '/student/student_dashboard.html';
        } else {
            alert('Rôle utilisateur non reconnu.');
            window.location.href = '/';
        }

    } catch (error) {
        console.error('Erreur réseau:', error);
        alert(error.message || 'Erreur réseau, veuillez réessayer plus tard.');
    }
});