const loginFom = document.getElementById('login-form');

loginFom.addEventListener('submit', event => {
    event.preventDefault();

    const loginUsername = document.getElementById('login-username').value;
    const loginPassword = document.getElementById('login-password').value;

    authentificate(loginUsername, loginPassword)

})

function authentificate(username, password) {
    console.log(username);
    console.log(password);

    const loginParams = {
        name: 'api_login',
        methods: ['POST']
    }

        // fetch(, loginParams)
            .then(response => response.json())
            .then(data => console.log(data))
            .catch(error => console.error(error))
}