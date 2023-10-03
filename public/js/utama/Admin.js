const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const tambahAdminForm = document.querySelector('form#tambahAdmin');
// const logoutForms = document.querySelectorAll('form#logoutForm');
const popup = document.querySelector('div#popup');
const redPopup = document.querySelector('div#redPopup');
const greenPopup = document.querySelector('div#greenPopup');
const inpEmail = document.getElementById('inpEmail');
const inpPassword = document.getElementById('inpPassword');
const logoutForms = document.querySelectorAll('form#logoutForm');
// console.log(logoutForms);
function showLoading(){
    document.querySelector('div#preloader').style.display = 'block';
}
function closeLoading(){
    document.querySelector('div#preloader').style.display = 'none';
}
showForm = function(condition, id_event = null, numRow = null){
    if(condition == 'tambah'){
        setTimeout(() => {
            divTambahEvent.style.display = 'block';
        }, 200);
    }else if(condition == 'edit'){
        setTimeout(() => {
            divEditEvent.style.display = 'block';
            // editEventForm.getElementById().value = 
            editEventForm.getElementById('inpIdEvent').value = id_event;
        }, 200);
    }else if(condition == 'hapus'){
        setTimeout(() => {
            divHapusEvent.querySelector('#btnHapusEvent').onclick = function(){
                hapusEvent(id_event,numRow);
            }
            divHapusEvent.style.display = 'block';
        }, 200);
    }
}
closeForm = function(condition){
    if(condition == 'tambah'){
        setTimeout(() => {
            divTambahEvent.style.display = 'none';
        }, 200);
    }else if(condition == 'edit'){
        setTimeout(() => {
            divEditEvent.style.display = 'none';
        }, 200);
    }else if(condition == 'hapus'){
        setTimeout(() => {
            divHapusEvent.querySelector('#btnHapusEvent').onclick = function(){
                hapusEvent();
            }
            divHapusEvent.style.display = 'none';
        }, 200);
    }
}
function tambahAdmin(){
    showLoading();
    var xhr = new XMLHttpRequest();
    var requestBody = {
        id_user: email,
        role:role,
        new_name:inpNewName.value,
        new_email:inpNewEmail.value,
        new_password:inpNewPassword.value,
        newRole:inpNewRole.value
    };
    xhr.open('POST', "/users/logout");
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    //send the form data
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showGreenPopup(response);
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    }
}
function logout(){
    var xhr = new XMLHttpRequest();
    var requestBody = {
        email: email,
        number:number
    };
    //open the request
    xhr.open('POST', "/users/logout");
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    //send the form data
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                window.location.reload();
            } else {
            }
        }
    }
}
function showGreenPopup(data, div = null){
    if(div == 'dashboard'){
        greenPopup.innerHTML = `
            <div class="bg" onclick="closePopup('green',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <div class="icon"><img src="${window.location.origin}/assets/img/check.png" alt=""></div>
            </div>
            <span class="closePopup" onclick="closePopup('green',true)">X</span>
            <label>${data.message}</label>
        `;
        greenPopup.style.display = 'block';
        setTimeout(() => {
            dashboardPage();
        }, 3000);
    }else{
        let dataa = JSON.stringify(data);
        if(dataa.includes('logout') ||dataa.includes('Logout') ){
            greenPopup.innerHTML = `
                <div class="bg" onclick="closePopup('green',true)"></div>
                <div class="kotak">
                    <div class="bunder1"></div>
                    <div class="icon"><img src="${window.location.origin}/public/img/icon/check.png" alt=""></div>
                </div>
                <span class="closePopup" onclick="closePopup('green',true)">X</span>
                <label>${dataa}</label>
            `;
            greenPopup.style.display = 'block';
            setTimeout(() => {
                closePopup('green');
            }, 3000);
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
            setTimeout(() => {
                closePopup('green');
            }, 3000);
        }
    }
}
function showRedPopup(data){
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
function closePopup(div, click = false) {
    if(click){
        if (div == 'green') {
            greenPopup.style.display = 'none';
            greenPopup.innerHTML = '';
        } else if (div == 'red') {
            redPopup.style.display = 'none';
            redPopup.innerHTML = '';
        }
    }else{
        if (div == 'green') {
            greenPopup.classList.add('fade-out');
            setTimeout(() => {
                greenPopup.style.display = 'none';
                greenPopup.classList.remove('fade-out');
                greenPopup.innerHTML = '';
            }, 750);
        } else if (div == 'red') {
            redPopup.classList.add('fade-out');
            setTimeout(() => {
                redPopup.style.display = 'none';
                redPopup.classList.remove('fade-out');
                redPopup.innerHTML = '';
            }, 750);
        }
    }
}