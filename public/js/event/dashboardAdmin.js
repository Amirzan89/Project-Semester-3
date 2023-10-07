const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const tableEvent = document.getElementById('tableEvent');
const divTambahEvent = document.getElementById('divTambahEvent');
const divEditEvent = document.getElementById('divEditEvent');
const divHapusEvent = document.getElementById('divHapusEvent');
const popup = document.querySelector('div#popup');
const redPopup = document.querySelector('div#redPopup');
const greenPopup = document.querySelector('div#greenPopup');
//tambah form
const tambahEventForm = document.getElementById('tambahEventForm');
const inpNamaEvent = tambahEventForm.querySelector('#inpNamaEvent');
const inpDeskripsiEvent = tambahEventForm.querySelector('#inpDeskripsiEvent');
const inpKategoriEvent = tambahEventForm.querySelector('#inpKategoriEvent');
const inpTAwalEvent = tambahEventForm.querySelector('#inpTAwalEvent');
const inpTAkhirEvent = tambahEventForm.querySelector('#inpTAkhirEvent');
const inpPendaftaranEvent = tambahEventForm.querySelector('#inpPendaftaranEvent');
const inpPosterEvent = tambahEventForm.querySelector('#inpPosterEvent');
//edit form
const editEventForm = document.getElementById('editEventForm');
const inpENamaEvent = editEventForm.querySelector('#inpENamaEvent');
const inpEDeskripsiEvent = editEventForm.querySelector('#inpEDeskripsiEvent');
const inpEKategoriEvent = editEventForm.querySelector('#inpEKategoriEvent');
const inpETAwalEvent = editEventForm.querySelector('#inpETAwalEvent');
const inpETAkhirEvent = editEventForm.querySelector('#inpETAkhirEvent');
const inpEPendaftaranEvent = editEventForm.querySelector('#inpEPendaftaranEvent');
const inpEPosterEvent = editEventForm.querySelector('#inpEPosterEvent');
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
};
function showLoading(){
    document.querySelector('div#preloader').style.display = 'block';
}
function closeLoading(){
    document.querySelector('div#preloader').style.display = 'none';
}
showForm = function(condition, IdEvent = null, numRow = null){
    if(condition == 'verifikasi'){
        setTimeout(() => {
            divEditEvent.style.display = 'block';
            var foundEvent = dataEvents.find(function(event){  
                return event.id_event === IdEvent;
            });

            if (foundEvent) {
                editEventForm.querySelector('#IDEvent').value = [foundEvent.id_event,numRow];
                inpENamaEvent.value = foundEvent.nama_event;
                inpEDeskripsiEvent.value = foundEvent.deskripsi_event;
                //set kategori
                var kategori = foundEvent.kategori_event;
                for (var i = 0; i < inpEKategoriEvent.options.length; i++) {
                    var option = inpEKategoriEvent.options[i];
                    if (kategori.includes(option.value.toUpperCase())) {
                        option.selected = true;
                    }
                }
                inpETAwalEvent.value = foundEvent.tanggal_awal_event;
                inpETAkhirEvent.value = foundEvent.tanggal_akhir_event;
                inpEPendaftaranEvent.value = foundEvent.link_pendaftaran;
                inpEPosterEvent.value = foundEvent.poster_event;
                console.log(foundEvent);
            }
        }, 200);
    }else if(condition == 'setuju'){
        setTimeout(() => {
            closeForm('hapus');
            divHapusEvent.querySelector('#btnHapusEvent').onclick = function(){
                hapusEvent(id_event,numRow);
            }
            divHapusEvent.style.display = 'block';
        }, 200);
    }else if(condition == 'tolak'){
        setTimeout(() => {
            closeForm('hapus');
            divHapusEvent.querySelector('#btnHapusEvent').onclick = function(){
                hapusEvent(id_event,numRow);
            }
            divHapusEvent.style.display = 'block';
        }, 200);
    }
}
closeForm = function(condition){
    if(condition == 'verifikasi'){
        setTimeout(() => {
            divTambahEvent.style.display = 'none';
        }, 200);
    }else if(condition == 'setuju'){
        setTimeout(() => {
            editEventForm.reset();
            divEditEvent.style.display = 'none';
        }, 200);
    }else if(condition == 'tolak'){
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
    const selectedDateAWal = new Date(tanggalAwal);
    const selectedDateAkhir = new Date(tanggalAkhir);
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
    const [dateAwal, timeAwal] = tanggalAwal.split('T');
    const [dateAkhir, timeAkhir] = tanggalAkhir.split('T');
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
    //change date format and time
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
                tambahEventForm.reset();
                inpTAwalEvent.value = currentDate.toISOString().substring(0, 16);
                inpTAkhirEvent.value = currentDate.toISOString().substring(0, 16);
                var response = JSON.parse(xhr.responseText);
                //tambah data ke table event
                const dataTable = ['nama_event','tanggal_awal','tanggal_akhir'];
                var newRow = document.createElement('tr');
                var nCell = document.createElement('th');
                nCell.setAttribute('scope','row');
                //add number row
                var numRow = tableEvent.getElementsByTagName('tbody')[0].rows.length+1;
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
                //get id event
                id_event += 1;
                // Add button edit
                var btnCell = document.createElement('td');
                var editBtn = document.createElement('button');
                editBtn.textContent = 'edit';
                editBtn.onclick = ()=>{
                    showForm('edit', id_event,numRow);
                };
                btnCell.appendChild(editBtn);
                // Add button delete
                var delBtn = document.createElement('button');
                delBtn.textContent = 'hapus';
                delBtn.onclick = ()=>{
                    showForm('hapus', id_event,numRow);
                };
                btnCell.appendChild(delBtn);
                newRow.appendChild(btnCell);
                //tambah data ke array
                dataEvents.push(requestBody);
                console.log(dataEvents);
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
    console.log('ubah eventt');
    event.preventDefault();
    const IDEvent = editEventForm.querySelector('#IDEvent').value;
    var [id_event, numRow] = IDEvent.split(',');
    const namaEvent = inpENamaEvent.value;
    const deskripsiEvent = inpEDeskripsiEvent.value;
    const kategoriEvent = inpEKategoriEvent.value;
    const tanggalAwal = inpETAwalEvent.value;
    const tanggalAkhir = inpETAkhirEvent.value;
    const pendaftaranEvent = inpEPendaftaranEvent.value;
    const selectedDateAWal = new Date(tanggalAwal);
    const selectedDateAkhir = new Date(tanggalAkhir);
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
    const [dateAwal, timeAwal] = tanggalAwal.split('T');
    const [dateAkhir, timeAkhir] = tanggalAkhir.split('T');
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
        id_event:id_event,
        nama_event:namaEvent,
        deskripsi_event:deskripsiEvent,
        kategori_event:kategoriEvent,
        tanggal_awal_event:formattedTimeAwal+" "+tanggalIAwal,
        tanggal_akhir_event:formattedTimeAkhir+" "+tanggalIAkhir,
        link_pendaftaran:pendaftaranEvent.value,
    };
    var xhr = new XMLHttpRequest();
    xhr.open('PUT', "/event/edit");
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                //edit data di event array
                var foundEvent = dataEvents.find(function(event){  
                    return event.id_event === id_event;
                });
                if (foundEvent) {
                    delete requestBody['id_user'];
                    delete requestBody['id_event'];
                    Object.assign(foundEvent,requestBody);
                }
                //ubah tabel
                numRow -= 1;
                var tbody = tableEvent.getElementsByTagName('tbody')[0];
                var rows = tbody.getElementsByTagName('tr');
                if (numRow >= 0 && numRow < rows.length) {
                    console.log('ganti data event');
                    var cells = rows[numRow].getElementsByTagName('td');
                        var dataTable = [namaEvent,formattedTimeAwal+" "+tanggalIAwal,formattedTimeAkhir+" "+tanggalIAkhir];
                        for(var i = 0; i < cells.length-1; i++){
                            cells[i].textContent = dataTable[i]; 
                        }
                }
                closeForm('edit');
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
                closeForm('hapus');
                var response = JSON.parse(xhr.responseText);
                if (numRow >= 1 && numRow <= tableEvent.getElementsByTagName('tbody')[0].rows.length){
                    tableEvent.querySelector('tbody').removeChild(tableEvent.getElementsByTagName('tbody')[0].rows[numRow-1]);
                } else {
                    console.error('Invalid row number');
                }
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
        number: number,
    };
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