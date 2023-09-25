const dropArea = document.querySelector('div#drag-area');
const inpJudul = document.querySelector('#judul');
const inpIsi = document.querySelector('#isi_artikel');
// const inpFile = dropArea.querySelector('#inpFile');
// const dragHeader = dropArea.querySelector('header');
const formTambah = document.querySelector('form#tambahArtikel');
const btnToken = document.querySelector('button#token');
const formDevice = document.querySelector('form#tambahDevice');
// const inpNamaDevice = formDevice.querySelector('#inpNama');
// const inpToken = formDevice.querySelector('#inpToken');
// const inpGps = formDevice.querySelector('#inpGps');
const inputOtp = document.querySelectorAll('.input input');
var file, gps;
// document.querySelector('div.editPopup').addEventListener('click',()=>{
//     document.querySelector('div.editPopup').style.display = 'none';
// });
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
inputOtp[5].addEventListener('keyup',(event)=>{
    event.preventDefault();
    var data = '', isInputEmpty = false; 
    for (var i = 0; i < inputOtp.length; i++) {
        var input = inputOtp[i];
        if (input.value.trim() == '') {
            console.log('harus diisi');
            isInputEmpty = true;
            break; // Exit from the for loop
        }
        data += input.value;
    }
    if(isInputEmpty){
        return;
    }
    var xhr = new XMLHttpRequest();
    var requestBody = {
        email: 'Admin@gmail.com',
        otp:data,
    };
    //open the request
    xhr.open('POST',"/users/")
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    //send the form data
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                // console.log('email   '+email);
                loginForm.reset(); //reset form after AJAX success.
                // window.location.reload();
                showPopup(response);
            } else {
                var response = JSON.parse(xhr.responseText);
                showPopup(response);
            }
        }
    }
    return false;
})
function showPopup(data){
    if(data.status  == 'success'){
        popup.innerHTML = `
            <div class="bg"></div>
            <div class="content">
                <p> ${data.message}</p>
                <button class="single" onclick="dashboardPage()">Login</button>
            </div>
        `;
        popup.style.display = 'flex';
    }else{
        if(data.includes('logout') ||data.includes('Logout') ){
            popup.innerHTML = `
                <div class="bg"></div>
                <div class="content">
                    <p>${data}</p>
                    <button class="single"onclick="closePopup()">OK</button>
                </div>
            `;
            popup.style.display = 'flex';
        }else{
            if(data.message){
                popup.innerHTML = `
                <div class="bg"></div>
                <div class="content">
                    <p>${data.message}</p>
                    <button class="single"onclick="closePopup()">OK</button>
                </div>
                `;
                popup.style.display = 'flex';
            }else{
                popup.innerHTML = `
                <div class="bg"></div>
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
// formDevice.onsubmit = (event)=>{
//     event.preventDefault();
//     var xhr = new XMLHttpRequest();
//     var requestBody = {
//         email: 'Admin@gmail.com',
//         nama:inpNamaDevice.value,
//         token: inpToken.value,
//         gps:gps
//     };
//     //open the request
//     xhr.open('POST',"/users/login")
//     xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
//     xhr.setRequestHeader('Content-Type', 'application/json');
//     //send the form data
//     xhr.send(JSON.stringify(requestBody));
//     xhr.onreadystatechange = function() {
//         if (xhr.readyState == XMLHttpRequest.DONE) {
//             if (xhr.status === 200) {
//                 var response = JSON.parse(xhr.responseText);
//                 // console.log('email   '+email);
//                 loginForm.reset(); //reset form after AJAX success.
//                 // window.location.reload();
//                 showPopup(response);
//             } else {
//                 var response = JSON.parse(xhr.responseText);
//                 showPopup(response);
//             }
//         }
//     }
//     return false;
// }
// formTambah.onsubmit = (event)=>{
//     event.preventDefault();
//     var formData = new FormData(formTambah);
//     formData.append('email', 'Admin@gmail.com');
//     formData.append('judul', inpJudul.value);
//     formData.append('isi_artikel', inpIsi.value);
//     formData.append('foto', file);
//     fetch('/page/edukasi', {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': csrfToken,
//         },
//         body: formData,
//     }).then(response => response.json()).then(data => {
//             console.log(data);
//             formTambah.reset();
//             dropArea.querySelector('img').remove();
//             dropArea.innerHTML = `<header>Drop File</header>`
//             dropArea.classList.remove('active');
//             showPopup(data);
//     }).catch(error => {
//         console.error(error);
//         showPopup(error);
//     });
//     return false; 
// }
function showFile(){
    let fileType = file.type;
    let validFile = ['image/jpg','image/png','image/jpeg'];
    if(validFile.includes(fileType)){
        console.log(fileType)
        dropArea.classList.add('active');
        let fileReader = new FileReader();
        fileReader.onload = () =>{
            let fileUrl = fileReader.result;
            let imgTag = `<img src="${fileUrl}" alt ="">`;
            dropArea.innerHTML = imgTag;
        }
        fileReader.readAsDataURL(file);
        dropArea.style.cursor = 'default';
    }else{
        console.log(fileType)
        dropArea.classList.remove('active');
    }
    console.log(file);
}
// document.querySelector('div.content').addEventListener('click', (event) => {
//     event.stopPropagation();
// });
// dropArea.addEventListener('click', (event) => {
//     if (event.target === dropArea) {
//         event.preventDefault();
//         inpFile.click();
//     }
// });
// inpFile.addEventListener('change',(event)=>{
//     file = event.target.files[0];
//     console.log('fileee')
//     console.log(file)
//     showFile();
// })
// dropArea.addEventListener('dragover',(event)=>{
//     event.preventDefault();
//     dropArea.classList.add('active');
//     dragHeader.textContent = 'release File';
// });
// dropArea.addEventListener('dragleave',(event)=>{
//     event.preventDefault();
//     dropArea.classList.remove('active');
//     dragHeader.textContent = 'Drop File';
// });
// dropArea.addEventListener('drop',(event)=>{
//     event.preventDefault();
//     file = event.dataTransfer.files[0];
//     showFile();
// });
function showEdit(data){
    document.querySelector(data).style.display = 'block';
    // document.querySelector('div#sendEmail').style.display = 'block';
}
function closeEdit(data){
    document.querySelector(data).style.display = 'none';
    // document.querySelector('div#sendEmail').style.display = 'none';
}
