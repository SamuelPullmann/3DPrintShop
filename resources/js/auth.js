document.addEventListener('DOMContentLoaded', function () {
    const title = document.getElementById('auth-title');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const showRegisterBottom = document.getElementById('show-register-bottom');
    const showLoginBottom = document.getElementById('show-login-bottom');

    function switchToRegister(e) {
        e.preventDefault();
        title.textContent = 'Register';
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
    }

    function switchToLogin(e) {
        e.preventDefault();
        title.textContent = 'Login';
        registerForm.style.display = 'none';
        loginForm.style.display = 'block';
    }

    showRegisterBottom.addEventListener('click', switchToRegister);
    showLoginBottom.addEventListener('click', switchToLogin);
});

