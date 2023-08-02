const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const popup = document.querySelector('div#popup');
const inpEmail = document.getElementById('inpEmail');
const inpPassword = document.getElementById('inpPassword');
const loginForm = document.getElementById('loginForm');
function showLoading(){
    document.querySelector('div#preloader').style.display = 'block';
}
function closeLoading(){
    document.querySelector('div#preloader').style.display = 'none';
}
loginForm.onsubmit = function(event){
    event.preventDefault();
    const email = inpEmail.value;
    const password = inpPassword.value;
    if (email.trim() === '') {
        showPopup('Email harus diisi !');
        return;
    }
    if (password.trim() === '') {
        showPopup('Password harus diisi !');
        return;
    }
    showLoading();
    var xhr = new XMLHttpRequest();
    var requestBody = {
        email: inpEmail.value,
        password:inpPassword.value
    };
    //open the request
    xhr.open('POST',"/users/login")
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    //send the form data
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showPopup(response);
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showPopup(response);
            }
        }
    }
    return false; 
}
function dashboardPage(){
    closePopup();
    window.location.href = '/page/dashboard';
}
function showPopup(data){
    if(data.status  == 'success'){
        popup.innerHTML = `
            <div class="bg" onclick="dashboardPage()"></div>
            <div class="content">
                <p> ${data.message}</p>
                <button class="single" onclick="dashboardPage()">Login</button>
            </div>
            `;
            popup.style.display = 'flex';
    }else{
        let dataa = JSON.stringify(data);
        if(dataa.includes('logout') ||dataa.includes('Logout') ){
            popup.innerHTML = `
            <div class="bg" onclick="closePopup()"></div>
            <div class="content">
                <p>${dataa}</p>
                <button class="single"onclick="closePopup()">OK</button>
            </div>
            `;
            popup.style.display = 'flex';
        }else{
            if(data.message){
                popup.innerHTML = `
                <div class="bg" onclick="closePopup()"></div>
                <div class="content">
                    <p>${data.message}</p>
                    <button class="single"onclick="closePopup()">OK</button>
                </div>
                `;
                popup.style.display = 'flex';
            }else{
                popup.innerHTML = `
                <div class="bg" onclick="closePopup()"></div>
                <div class="content">
                    <p>${data}</p>
                    <button class="single"onclick="closePopup()">OK</button>
                </div>
                `;
                popup.style.display = 'flex';
            }
        }
    }
}
function closePopup() {
    popup.style.display = 'none';
    popup.innerHTML = '';
}