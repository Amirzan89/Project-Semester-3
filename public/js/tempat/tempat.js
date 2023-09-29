const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const tambahEventForms = document.getElementById('tambahEventForm');
const logoutForms = document.querySelectorAll('form#logoutForm');
const popup = document.querySelector('div#popup');
const redPopup = document.querySelector('div#redPopup');
const greenPopup = document.querySelector('div#greenPopup');
const inpNamaEvent = document.getElementById('inpNamaEvent');
const inpDeskripsiEvent = document.getElementById('inpDeskripsiEvent');
const inpKategoriEvent = document.getElementById('inpKategoriEvent');
const inpTAwalEvent = document.getElementById('inpTAwalEvent');
const inpTAkhirEvent = document.getElementById('inpTAkhirEvent');
const inpPendaftaranEvent = document.getElementById('inpPendaftaranEvent');
const inpPosterEvent = document.getElementById('inpPosterEvent');
const formatFile = {
    image: ["image/jpeg", "image/png"],
    pdf: "application/pdf",
};
const currentDate = new Date();
const opt = {
    timeZone:"Asia/Jakarta",
    hour12:false
}
inpTAwalEvent.value = currentDate.toLocaleString('id-ID', opt);
inpTAkhirEvent.value = currentDate.toLocaleString('id-ID', opt);
tanggalSekarang = new Date(currentDate.toISOString().substring(0, 16));
inpTAwalEvent.value = currentDate.toISOString().substring(0, 16);
inpTAkhirEvent.value = currentDate.toISOString().substring(0, 16);
console.log("tanggal sekarang "+currentDate.toISOString().substring(0, 16));
dateAwalSebelum = currentDate.toISOString().substring(0, 16);
dateAkhirSebelum = currentDate.toISOString().substring(0, 16);
inpTAwalEvent.onchange = function (event) {
    const selectedDatetimeAwal = inpTAwalEvent.value;
    const selectedDatetimeAkhir = inpTAkhirEvent.value;
    const selectedDateAwal = new Date(selectedDatetimeAwal);
    const selectedDateAkhir = new Date(selectedDatetimeAkhir);
    if (selectedDateAwal > selectedDateAkhir) {
        showRedPopup("tanggal awal lebih lama dari tanggal akhir");
        inpTAwalEvent.value = dateAwalSebelum;
        return;
    }
    if(selectedDateAwal < tanggalSekarang){
        showRedPopup("invalid waktu");
        inpTAwalEvent.value = currentDate.toISOString().substring(0, 16);
        return;
    }
    dateAwalSebelum = selectedDatetimeAwal;
};
inpTAkhirEvent.onchange = function (event) {
    const selectedDatetimeAwal = inpTAwalEvent.value;
    const selectedDatetimeAkhir = inpTAkhirEvent.value;
    console.log("tanggal akhir "+selectedDatetimeAkhir);
    const selectedDateAwal = new Date(selectedDatetimeAwal);
    const selectedDateAkhir = new Date(selectedDatetimeAkhir);
    if (selectedDateAwal > selectedDateAkhir) {
        showRedPopup("tanggal awal lebih lama dari tanggal akhir");
        inpTAkhirEvent.value = dateAkhirSebelum;
        return;
    }
    if(selectedDateAkhir < tanggalSekarang){
        showRedPopup("invalid waktu");
        inpTAkhirEvent.value = currentDate.toISOString().substring(0, 16);
        return;
    }
    dateAkhirSebelum = selectedDatetimeAkhir;
    const [datePart, timePart] = selectedDatetimeAwal.split('T');
    // Output the date and time
    console.log("Date:", datePart);
    // const [datePartAwal, timePartAwal] = selectedDatetimeAwal.split('T');
    // // Output the date and time
    // console.log("Date:", datePartAwal);
    // console.log("Time:", timePartAwal);
    // const [datePart, timePart] = selectedDatetimeAwal.split('T');
    // // Output the date and time
    // console.log("Date:", datePart);
    // console.log("Time:", timePart);

};
function showLoading(){
    document.querySelector('div#preloader').style.display = 'block';
}
function closeLoading(){
    document.querySelector('div#preloader').style.display = 'none';
}
const dataUpload = {
    url:'/users/upload',
    maxFileSize:'10MB'
}
tambahEventForms.onsubmit = function(event){
    event.preventDefault();
    console.log("tambah event");    
    const namaEvent = inpNamaEvent.value;
    const deskripsiEvent = inpDeskripsiEvent.value;
    const kategoriEvent = inpKategoriEvent.value;
    const tanggalAwal = inpTAwalEvent.value;
    const tanggalAkhir = inpTAkhirEvent.value;
    const pendaftaranEvent = inpPendaftaranEvent.value;
    const selectedDatetimeAwal = inpTAwalEvent.value;
    const selectedDatetimeAkhir = inpTAkhirEvent.value;
    const selectedDateAWal = new Date(selectedDatetimeAwal);
    const selectedDateAkhir = new Date(selectedDatetimeAkhir);
    if (namaEvent.trim() === '') {
        showRedPopup('nama event harus diisi !');
        return;
    }
    if (kategoriEvent.trim() === '') {
        showRedPopup('kategori harus diisi !');
        return;
    }
    if (tanggalAwal.trim() === '') {
        showRedPopup('tanggal awal harus diisi !');
        return;
    }
    if (tanggalAkhir.trim() === '') {
        showRedPopup('tanggal akhir harus diisi !');
        return;
    }
    if (selectedDateAWal > selectedDateAkhir) {
        showRedPopup("tanggal awal lebih lama dari tanggal akhir")
    }
    //convert to date and time
    const [datePart, timePart] = selectedDatetimeAwal.split('T');
    // Output the date and time
    console.log("Date:", datePart);
    const hours = selectedDateAkhir.getUTCHours();
    const minutes = selectedDateAkhir.getUTCMinutes();

    // Format the time in 24-hour format (HH:MM)
    const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    //change date format and time`
    showLoading();
    var requestBody = {
        id_user: idUser,
        nama_event:namaEvent,
        deskripsi:deskripsiEvent,
        kategori:kategoriEvent,
        tanggal_awal:tanggalAwal,
        tanggal_akhir:tanggalAkhir,
        link:pendaftaranEvent.value,
    };
    console.log("data form");
    console.log(JSON.stringify(requestBody));
    // closeLoading();
    var xhr = new XMLHttpRequest();
        var requestBody = {
            email: email,
            number: number,
        };
        //open the request
        xhr.open('POST', "/event/tambah");
        // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Content-Type', 'application/json');
        //send the form data
        xhr.send(JSON.stringify(requestBody));
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = xhr.responseText;
                    showGreenPopup(response);
                    // form.reset();
                    // window.location.reload();
                } else {
                    var response = xhr.responseText;
                    showRedPopup(response);
                }
            }
        }
    return false; 
}
logoutForms.forEach(function(form) {
    form.onsubmit = function(event){
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        var requestBody = {
            email: email,
            number: number,
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
                    var response = xhr.responseText;
                    form.reset();
                    window.location.reload();
                } else {
                }
            }
        }
        return false; 
    }
});
function showGreenPopup(data, div = null){
    // if(div == 'dashboard'){
    //     greenPopup.innerHTML = `
    //         <div class="bg" onclick="closePopup('green',true)"></div>
    //         <div class="kotak">
    //             <div class="bunder1"></div>
    //             <div class="icon"><img src="${window.location.origin}/assets/img/check.png" alt=""></div>
    //         </div>
    //         <span class="closePopup" onclick="closePopup('green',true)">X</span>
    //         <label>${data.message}</label>
    //     `;
    //     greenPopup.style.display = 'block';
    //     setTimeout(() => {
    //         dashboardPage();
    //     }, 3000);
    // }else{
        let dataa = JSON.stringify(data);
        // if(dataa.includes('logout') ||dataa.includes('Logout') ){
        //     greenPopup.innerHTML = `
        //         <div class="bg" onclick="closePopup('green',true)"></div>
        //         <div class="kotak">
        //             <div class="bunder1"></div>
        //             <div class="icon"><img src="${window.location.origin}/public/img/icon/check.png" alt=""></div>
        //         </div>
        //         <span class="closePopup" onclick="closePopup('green',true)">X</span>
        //         <label>${dataa}</label>
        //     `;
        //     greenPopup.style.display = 'block';
        //     setTimeout(() => {
        //         closePopup('green');
        //     }, 3000);
        // }else{
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
        // }
    // }
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