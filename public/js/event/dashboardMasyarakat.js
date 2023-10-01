const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const tableEvent = document.getElementById('tableEvent');
const divTambahEvent = document.getElementById('divTambahEvent');
const divEditEvent = document.getElementById('divEditEvent');
const divHapusEvent = document.getElementById('divHapusEvent');
const tambahEventForm = document.getElementById('tambahEventForm');
const editEventForm = document.getElementById('editEventForm');
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
console.log(tableEvent.getElementsByTagName('tbody'));
console.log(tableEvent.getElementsByTagName('tbody').rows);
console.log(tableEvent.getElementsByTagName('tbody')[0].rows);
// console.log(tableEvent.getElementsByTagName('tbody')[0].rows[]);
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
};
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
const dataUpload = {
    url:'/users/upload',
    maxFileSize:'10MB'
}
tambahEventForm.onsubmit = function(event){
    console.log('tambah eventt');
    event.preventDefault();
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
    //convert to date time
    const [dateAwal, timeAwal] = selectedDatetimeAwal.split('T');
    const [dateAkhir, timeAkhir] = selectedDatetimeAkhir.split('T');
    //convert date 
    const [yearAwal, monthAwal, dayAwal] = dateAwal.split('-');
    const [yearAkhir, monthAkhir, dayAkhir] = dateAkhir.split('-');
    const tanggalIAwal = dayAwal + '-'+ monthAwal +'-' + yearAwal;
    const tanggalIAkhir = dayAkhir + '-' + monthAkhir + '-' + yearAkhir;
    //convert time
    const hourAwal = selectedDateAWal.getUTCHours();
    const minuteAwal = selectedDateAWal.getUTCMinutes();
    const hourAkhir = selectedDateAkhir.getUTCHours();
    const minuteAkhir = selectedDateAkhir.getUTCMinutes();
    // Format the time in 24-hour format (HH:MM)
    const formattedTimeAwal = `${hourAwal.toString().padStart(2, '0')}:${minuteAwal.toString().padStart(2, '0')}`;
    const formattedTimeAkhir = `${hourAkhir.toString().padStart(2, '0')}:${minuteAkhir.toString().padStart(2, '0')}`;
    //change date format and time`
    showLoading();
    var requestBody = {
        id_user: idUser,
        nama_event:namaEvent,
        deskripsi:deskripsiEvent,
        kategori:kategoriEvent,
        tanggal_awal:formattedTimeAwal+" "+tanggalIAwal,
        tanggal_akhir:formattedTimeAkhir+" "+tanggalIAkhir,
        link:pendaftaranEvent.value,
    };
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "/event/tambah");
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                //tambah data ke table event
                const dataTable = ['nama_event','tanggal_awal','tanggal_akhir'];
                var newRow = document.createElement('tr');
                //add number row
                var nCell = document.createElement('th');
                nCell.setAttribute('scope','row');
                var numRow = tableEvent.getElementsByTagName('tbody')[0].rows.length+1
                nCell.textContent = numRow; 
                newRow.appendChild(nCell);
                //add data row
                for (var i = 0; i < dataTable.length; i++) {
                    var key = dataTable[i];
                    if (requestBody.hasOwnProperty(key)) {
                        var newCell = document.createElement('td');
                        newCell.textContent = requestBody[key];
                        newRow.appendChild(newCell);
                    }
                }
                tableEvent.querySelector('tbody').appendChild(newRow);
                // //add button edit
                // var editCell = document.createElement('td');
                // var editBtn = document.createElement('button');
                // editBtn.onclick = function(){
                //     showForm('edit',id_event);
                // }
                // editCell.appendChild(editBtn);
                // newRow.appendChild(editCell);
                // //add button delete
                // var delCell = document.createElement('td');
                // var delBtn = document.createElement('button');
                // delBtn.onclick = function(){
                //     showForm('hapus',id_event);
                // }
                // delCell.appendChild(delBtn);
                // newRow.appendChild(delCell);
                // Add button edit
                var editCell = document.createElement('td');
                var editBtn = document.createElement('button');
                editBtn.textContent = 'Edit';
                editBtn.onclick = function () {
                    showForm('edit', id_event,numRow);
                };
                editCell.appendChild(editBtn);
                newRow.appendChild(editCell);
                
                // Add button delete
                var delCell = document.createElement('td');
                var delBtn = document.createElement('button');
                delBtn.textContent = 'hapus';
                delBtn.onclick = function () {
                    showForm('hapus', id_event,numRow);
                };
                delCell.appendChild(delBtn);
                newRow.appendChild(delCell);

                closeForm('tambah');
                //show popup
                showGreenPopup(response);
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    }
    return false; 
}
editEventForm.onsubmit = function(event){
    console.log('tambah eventt');
    event.preventDefault();
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
    //convert to date time
    const [dateAwal, timeAwal] = selectedDatetimeAwal.split('T');
    const [dateAkhir, timeAkhir] = selectedDatetimeAkhir.split('T');
    //convert date 
    const [yearAwal, monthAwal, dayAwal] = dateAwal.split('-');
    const [yearAkhir, monthAkhir, dayAkhir] = dateAkhir.split('-');
    const tanggalIAwal = dayAwal + '-'+ monthAwal +'-' + yearAwal;
    const tanggalIAkhir = dayAkhir + '-' + monthAkhir + '-' + yearAkhir;
    //convert time
    const hourAwal = selectedDateAWal.getUTCHours();
    const minuteAwal = selectedDateAWal.getUTCMinutes();
    const hourAkhir = selectedDateAkhir.getUTCHours();
    const minuteAkhir = selectedDateAkhir.getUTCMinutes();
    // Format the time in 24-hour format (HH:MM)
    const formattedTimeAwal = `${hourAwal.toString().padStart(2, '0')}:${minuteAwal.toString().padStart(2, '0')}`;
    const formattedTimeAkhir = `${hourAkhir.toString().padStart(2, '0')}:${minuteAkhir.toString().padStart(2, '0')}`;
    //change date format and time`
    showLoading();
    var requestBody = {
        id_user: idUser,
        nama_event:namaEvent,
        deskripsi:deskripsiEvent,
        kategori:kategoriEvent,
        tanggal_awal:formattedTimeAwal+" "+tanggalIAwal,
        tanggal_akhir:formattedTimeAkhir+" "+tanggalIAkhir,
        link:pendaftaranEvent.value,
    };
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "/event/edit");
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                //tambah data ke table event
                var newRow = document.createElement('tr');
                for (var key in eventData) {
                    if (eventData.hasOwnProperty(key)) {
                        var newCell = document.createElement('td');
                        newCell.textContent = eventData[key];
                        newRow.appendChild(newCell);
                    }
                }
                document.getElementById('tableEvent').querySelector('tbody').appendChild(newRow);
                showGreenPopup(response);
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    }
    return false; 
}
function hapusEvent(id_event, numRow){
    console.log('hapus eventt');
    showLoading();
    var requestBody = {
        id_user: idUser,
        id_event: id_event,
    };
    var xhr = new XMLHttpRequest();
    xhr.open('DELETE', "/event/delete");
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                tableEvent.getElementsByTagName('tbody')[0].rows;
                if (numRow >= 1 && numRow <= tableEvent.getElementsByTagName('tbody')[0].rows.length){
                    tableEvent.querySelector('tbody').removeChild(tableEvent.getElementsByTagName('tbody')[0].rows[numRow-1]);
                } else {
                    console.error('Invalid row number');
                }
                closeForm('hapus');
                showGreenPopup(response);
            } else {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                showRedPopup(response);
            }
        }
    }
}
logoutForms.forEach(function(form) {
    console.log('wayaae')
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
    let dataa = JSON.stringify(data);
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