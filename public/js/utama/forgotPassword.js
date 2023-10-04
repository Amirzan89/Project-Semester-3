const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const popup = document.querySelector('div#popup');
const forgotPasswordForm = document.getElementById('ForgotPassword');
const verifyOTPForm = document.getElementById('verifyOTP');
const verifyChangeForm = document.getElementById('verifyChange');
const emailInput = document.getElementById('email');
const passInput = document.getElementById('password');
const passNewInput = document.getElementById('password_new');
const inputOtp = verifyOTPForm.querySelectorAll('input[type="text"]');
var email, otp = '', div, timer,timerMenit,timerDetik;
function showLoading(){
    document.querySelector('div#preloader').style.display = 'block';
}
function closeLoading(){
    document.querySelector('div#preloader').style.display = 'none';
}
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
function startCountdown(waktu) {
    timer = setInterval(function() {
    var now = new Date().getTime();
    var distance = waktu - now;
    // Calculate time remaining
    timerMenit = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    timerDetik = Math.floor((distance % (1000 * 60)) / 1000);
        if (distance < 0) {
            clearInterval(timer);
            timer['timer'] = null;
        }
    }, 1000);
}
forgotPasswordForm.onsubmit = function(event){
    event.preventDefault();
    const Email = emailInput.value;
    if(Email.trim() == ''){
        showRedPopup('Email harus di isi');
        return;
    }
    if (!isValidEmail(Email)) {
        showRedPopup('Masukkan email dengan benar !');
        return;
    }   
    showLoading();
    var xhr = new XMLHttpRequest();
    email = emailInput.value;
    var requestBody = {
        email: emailInput.value
    };
    xhr.open('POST',"/verifikasi/create/password")
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                forgotPasswordForm.reset();
                var response = JSON.parse(xhr.responseText);
                var waktu = new Date(response.data.waktu).getTime();
                startCountdown(waktu);
                showGreenPopup(response,'otp');
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                if(response.data){
                    showRedPopup(response,'otp');
                }
                showRedPopup(response);
            }
        }
    }
    return false; 
}
inputOtp.forEach((input, index) => {
    input.addEventListener('input', (event) => {
        const value = event.target.value;
        if (value.length > 1) {
            event.target.value = value.slice(0, 1);
            return;
        }
        if (!isNaN(value)) {
            if (index < inputOtp.length - 1) {
                inputOtp[index + 1].focus();
            }
        } else {
            event.target.value = event.target.value.replace(/\D/g, '');
        }
        if (value === '') {
            if (index > 0) {
                inputOtp[index - 1].focus();
            }
        }
    });
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Backspace' && event.target.value === '') {
            if (index > 0) {
                inputOtp[index-1].focus();
            }
        }
    });
});
verifyOTPForm.onsubmit = function(event){
    event.preventDefault();
    var data = '', isInputEmpty = false; 
    for (var i = 0; i < inputOtp.length; i++) {
        var input = inputOtp[i];
        if (input.value.trim() == '') {
            isInputEmpty = true;
            showRedPopup('otp harus diisi !');
            break;
        }
        data += input.value;
    }
    if(isInputEmpty){
        return;
    }
    showLoading();
    otp = data;
    var xhr = new XMLHttpRequest();
    var requestBody = {
        email: email,
        code:data,
    };
    xhr.open('POST',"/verifikasi/otp/password")
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                verifyOTPForm.reset();
                showGreenPopup(response,'gantiPassword');
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    }
    return false; 
}
verifyChangeForm.onsubmit = function(event){
    event.preventDefault();
    const password = passInput.value;
    const confirmPassword = passNewInput.value;
    if (password.trim() === '') {
        showRedPopup('Password harus diisi !');
        return;
    }
    if (password.length < 8) {
        showRedPopup('Password minimal 8 karakter !');
        return;
    }
    if (!/[A-Z]/.test(password)) {
        showRedPopup('Password minimal ada 1 huruf kapital !');
        return;
    }
    if (!/[a-z]/.test(password)) {
        showRedPopup('Password minimal ada 1 huruf kecil !');
        return;
    }
    if (!/\d/.test(password)) {
        showRedPopup('Password minimal ada 1 angka !');
        return;
    }
    if (!/[!@#$%^&*]/.test(password)) {
        showRedPopup('Password minimal ada 1 karakter unik !');
        return;
    }
    if (confirmPassword.trim() === '') {
        showRedPopup('Password konfirmasi harus diisi !');
        return;
    }
    if (confirmPassword.length < 8) {
        showRedPopup('Password konfirmasi minimal 8 karakter !');
        return;
    }
    if (!/[A-Z]/.test(confirmPassword)) {
        showRedPopup('Password konfirmasi minimal ada 1 huruf kapital !');
        return;
    }
    if (!/[a-z]/.test(confirmPassword)) {
        showRedPopup('Password konfirmasi minimal ada 1 huruf kecil !');
        return;
    }
    if (!/\d/.test(confirmPassword)) {
        showRedPopup('Password konfirmasi minimal ada 1 angka !');
        return;
    }
    if (!/[!@#$%^&*]/.test(confirmPassword)) {
        showRedPopup('Password konfirmasi minimal ada 1 karakter unik !');
        return;
    }
    if(password != confirmPassword){
        showRedPopup('Password harus sama!');
        return;
    }
    showLoading();
    var requestBody; 
    if(otp === null || otp === ''){
        requestBody = {
            email: email,
            nama:nama,
            link:link,
            password:passInput.value,
            password_confirm:passNewInput.value,
            description:description
        };
    }else{
        requestBody = {
            email: email,
            code:otp,
            password:passInput.value,
            password_confirm:passNewInput.value,
            description:'changePass'
        };
    }
    var xhr = new XMLHttpRequest();
    xhr.open('POST',"/verifikasi/password");
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                if(otp == null || otp == ''){
                    showGreenPopup(response,'dashboard');
                }else{
                    showGreenPopup(response,'login');
                    // showGreenPopup(response);
                }
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    }
    return false; 
}
function sendOtp(){
    if (email && email.trim() !== '') {
        if(timer){
            showTimerPopup()
        }else{
            showLoading();
            var xhr = new XMLHttpRequest();
            var requestBody = {
                email: email,
            };
            xhr.open('POST',"/verifikasi/create/password");
            // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(JSON.stringify(requestBody));
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        closeLoading();
                        var response = JSON.parse(xhr.responseText);
                        var waktu = new Date(response.data.waktu).getTime();
                        startCountdown(waktu);
                        showGreenPopup(response);
                    } else {
                        closeLoading();
                        var response = JSON.parse(xhr.responseText);
                        showRedPopup(response)
                    }
                }
            }
        }
    }else{
        showRedPopup('Email harus diisi');
    }
}
function showTimerPopup(){
    redPopup.innerHTML = `
        <div class="bg" onclick="closePopup('red',true)"></div>
        <div class="kotak">
            <div class="bunder1"></div>
            <span>!</span>
        </div>
        <span class="closePopup" onclick="closePopup('red',true)">X</span>
        <label>sisa waktu ${timerMenit} menit ${timerDetik} detik untuk kirim kembali</label>
    `;
    redPopup.style.display = 'block';
    const labelElement = redPopup.querySelector('label');
    let second = 0;
    const intervalId = setInterval(() => {
        labelElement.textContent = `sisa waktu ${timerMenit} menit ${timerDetik} detik untuk kirim kembali`;
        second++;

        if (second >= 3) {
            clearInterval(intervalId); 
            closePopup('red');
        }
    }, 1000);
}
function loginPage(){
    window.location.href = '/login';
}
function dashboardPage(){
    window.location.href = '/dashboard';
}
function showGreenPopup(data, div = null){
    // let dataa = JSON.stringify(data);
    if(div){
        if(div == 'login'){
            greenPopup.innerHTML = `
                <div class="bg" onclick="closePopup('green',true,${div})"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <div class="icon"><img src="${window.location.origin}/public/img/icon/check.png" alt=""></div>
                </div>
                <span class="closePopup" onclick="closePopup('green',true,${div})">X</span>
                <label>Berhasil membuat akun silahkan login </label>
            `;
            greenPopup.style.display = 'block';
            setTimeout(() => {
                closePopup('green',false, div);
            }, 2000);
        }else if(div == 'dashboard'){
            greenPopup.innerHTML = `
                <div class="bg" onclick="closePopup('green',true,${div})"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <div class="icon"><img src="${window.location.origin}/public/img/icon/check.png" alt=""></div>
                </div>
                <span class="closePopup" onclick="closePopup('green',true,${div})">X</span>
                <label>Berhasil membuat akun mohon tunggu sebentar </label>
            `;
            greenPopup.style.display = 'block';
            setTimeout(() => {
                dashboardPage();
                closePopup('green',false,div);
            }, 2000);
        }else{
            greenPopup.innerHTML = `
                <div class="bg" onclick="closePopup('green',true)"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <div class="icon"><img src="${window.location.origin}/public/img/icon/check.png" alt=""></div>
                </div>
                <span class="closePopup" onclick="closePopup('green',true)">X</span>
                <label>${data.message}</label>
            `;
            greenPopup.style.display = 'block';
            showDiv(div);
            setTimeout(() => {
                closePopup('green',false, div);
            }, 3000);
        }
    }else{
        greenPopup.innerHTML = `
            <div class="bg" onclick="closePopup('green',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <div class="icon"><img src="${window.location.origin + tPath}/assets/img/check.png" alt=""></div>
            </div>
            <span class="closePopup" onclick="closePopup('green',true)">X</span>
            <label>${data.message}</label>
        `;
        greenPopup.style.display = 'block';
        setTimeout(() => {
            closePopup('green');
        }, 3000);
    }
}
function showRedPopup(data, div){
    if(div == 'otp'){
        redPopup.innerHTML = `
            <div class="bg" onclick="closePopup('red',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <span>!</span>
            </div>
            <span class="closePopup" onclick="closePopup('red',true)">X</span>
            <label>${data.message}</label>
        `;
        redPopup.style.display = 'block';
        showDiv(div);
        setTimeout(() => {
            closePopup('red');
        }, 3000);
    }else{
        if(data.message){
            redPopup.innerHTML = `
                <div class="bg" onclick="closePopup('red',true)"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <span>!</span>
                </div>
                <span class="closePopup" onclick="closePopup('red',true)">X</span>
                <label>${data.message}</label>
            `;
            redPopup.style.display = 'block';
            setTimeout(() => {
                closePopup('red');
            }, 3000);
        }else{
            redPopup.innerHTML = `
                <div class="bg" onclick="closePopup('red',true)"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <span>!</span>
                </div>
                <span class="closePopup" onclick="closePopup('red', true)">X</span>
                <label>${data}</label>
            `;
            redPopup.style.display = 'block';
            setTimeout(() => {
                closePopup('red');
            }, 3000);
        }
    }
}
function closePopup(opt,click = false, div= null) {
    if(click){
        if (opt == 'green') {
            greenPopup.style.display = 'none';
            greenPopup.innerHTML = '';
        } else if (opt == 'red') {
            redPopup.style.display = 'none';
            redPopup.innerHTML = '';
        }
    }else{
        if (opt == 'green') {
            greenPopup.classList.add('fade-out');
            setTimeout(() => {
                greenPopup.style.display = 'none';
                greenPopup.classList.remove('fade-out');
                greenPopup.innerHTML = '';
            }, 750);
        } else if (opt == 'red') {
            redPopup.classList.add('fade-out');
            setTimeout(() => {
                redPopup.style.display = 'none';
                redPopup.classList.remove('fade-out');
                redPopup.innerHTML = '';
            }, 750);
        }
    }
    if(div){
        if(div == 'login'){
            loginPage();
        }else{
            showDiv(div);
        }
    }
}
function showDiv(div){
    if(div == 'otp'){
        document.querySelector('div#sendEmail').style.display = 'none';
        document.querySelector('div#otp').style.display = 'block';
    }else if(div == 'gantiPassword'){
        document.querySelector('div#otp').style.display = 'none';
        document.querySelector('div#gantiPassword').style.display = 'block';
    }
}