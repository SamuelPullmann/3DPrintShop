document.addEventListener('DOMContentLoaded', function () {
    const title = document.getElementById('auth-title');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const showRegisterBottom = document.getElementById('show-register-bottom');
    const showLoginBottom = document.getElementById('show-login-bottom');
    const registerPasswordInput = document.getElementById('register-password');
    const registerPasswordError = document.getElementById('register-password-error');
    const registerButton = document.getElementById('register-button');

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

    function checkPasswordRequirements() {
        const password = registerPasswordInput.value;
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasDigit = /\d/.test(password);

        const isValid = password.length >= minLength && hasUpperCase && hasLowerCase && hasDigit;
        registerPasswordError.style.display = isValid ? 'none' : 'block';

        return isValid;
    }

    showRegisterBottom.addEventListener('click', switchToRegister);
    showLoginBottom.addEventListener('click', switchToLogin);

    registerForm.querySelector('form').addEventListener('submit', function(e) {
        if (!checkPasswordRequirements()) {
            e.preventDefault(); // Zastav submit len ak je heslo nevalidné
        }
    });
});

