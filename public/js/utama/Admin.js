const domain = window.location.protocol + '//' + window.location.hostname +":"+window.location.port;
const tableUser = document.getElementById('tableUser');
const divTambahUser = document.getElementById('divTambahUser');
const divEditUser = document.getElementById('divEditUser');
const divHapusUser = document.getElementById('divHapusUser');
const popup = document.querySelector('div#popup');
const redPopup = document.querySelector('div#redPopup');
const greenPopup = document.querySelector('div#greenPopup');
//tambah form
const tambahUserForm = document.getElementById('tambahUserForm');
const inpNamaUser = tambahUserForm.querySelector('#inpNamaUser');
const inpDeskripsiUser = tambahUserForm.querySelector('#inpDeskripsiUser');
const inpKategoriUser = tambahUserForm.querySelector('#inpKategoriUser');
const inpTAwalUser = tambahUserForm.querySelector('#inpTAwalUser');
const inpTAkhirUser = tambahUserForm.querySelector('#inpTAkhirUser');
const inpPendaftaranUser = tambahUserForm.querySelector('#inpPendaftaranUser');
const inpPosterUser = tambahUserForm.querySelector('#inpPosterUser');
//edit form
const editUserForm = document.getElementById('editUserForm');
const inpENamaUser = editUserForm.querySelector('#inpENamaUser');
const inpEKategoriUser = editUserForm.querySelector('#inpEKategoriUser');
const formatFile = {                             
    image: ["image/jpeg", "image/png"],
    pdf: "application/pdf",
};
const currentDate = new Date();
const opt = {
    timeZone:"Asia/Jakarta",
    hour12:false
}
// inpTAwalUser.value = currentDate.toLocaleString('id-ID', opt);
// inpTAkhirUser.value = currentDate.toLocaleString('id-ID', opt);
// tanggalSekarang = new Date(currentDate.toISOString().substring(0, 16));
// inpTAwalUser.value = currentDate.toISOString().substring(0, 16);
// inpTAkhirUser.value = currentDate.toISOString().substring(0, 16);
// dateAwalSebelum = currentDate.toISOString().substring(0, 16);
// dateAkhirSebelum = currentDate.toISOString().substring(0, 16);
// inpTAwalUser.onchange = function (event) {
//     const selectedDatetimeAwal = inpTAwalUser.value;
//     const selectedDatetimeAkhir = inpTAkhirUser.value;
//     const selectedDateAwal = new Date(selectedDatetimeAwal);
//     const selectedDateAkhir = new Date(selectedDatetimeAkhir);
//     if (selectedDateAwal > selectedDateAkhir) {
//         showRedPopup("tanggal awal lebih lama dari tanggal akhir");
//         inpTAwalUser.value = dateAwalSebelum;
//         return;
//     }
//     if(selectedDateAwal < tanggalSekarang){
//         showRedPopup("invalid waktu");
//         inpTAwalUser.value = currentDate.toISOString().substring(0, 16);
//         return;
//     }
//     dateAwalSebelum = selectedDatetimeAwal;
// };
// inpTAkhirUser.onchange = function (event) {
//     const selectedDatetimeAwal = inpTAwalUser.value;
//     const selectedDatetimeAkhir = inpTAkhirUser.value;
//     const selectedDateAwal = new Date(selectedDatetimeAwal);
//     const selectedDateAkhir = new Date(selectedDatetimeAkhir);
//     if (selectedDateAwal > selectedDateAkhir) {
//         showRedPopup("tanggal awal lebih lama dari tanggal akhir");
//         inpTAkhirUser.value = dateAkhirSebelum;
//         return;
//     }
//     if(selectedDateAkhir < tanggalSekarang){
//         showRedPopup("invalid waktu");
//         inpTAkhirUser.value = currentDate.toISOString().substring(0, 16);
//         return;
//     }
//     dateAkhirSebelum = selectedDatetimeAkhir;
// };
function showLoading(){
    document.querySelector('div#preloader').style.display = 'block';
}
function closeLoading(){
    document.querySelector('div#preloader').style.display = 'none';
}
showForm = function(condition, IdUser = null, numRow = null){
    if(condition == 'tambah'){
        setTimeout(() => {
            divTambahUser.style.display = 'block';
        }, 200);
    }else if(condition == 'edit'){
        setTimeout(() => {
            divEditUser.style.display = 'block';
            var foundUser = dataUsers.find(function(event){  
                return event.id_event === IdUser;
            });
            if (foundUser) {
                editUserForm.querySelector('#IDUser').value = [foundUser.id_event,numRow];
                inpENamaUser.value = foundUser.nama_event;
                inpEDeskripsiUser.value = foundUser.deskripsi_event;
                //set kategori
                var kategori = foundUser.kategori_event;
                for (var i = 0; i < inpEKategoriUser.options.length; i++) {
                    var option = inpEKategoriUser.options[i];
                    if (kategori.includes(option.value.toUpperCase())) {
                        option.selected = true;
                    }
                }
                inpETAwalUser.value = foundUser.tanggal_awal_event;
                inpETAkhirUser.value = foundUser.tanggal_akhir_event;
                inpEPendaftaranUser.value = foundUser.link_pendaftaran;
                inpEPosterUser.value = foundUser.poster_event;
                console.log(foundUser);
            }
        }, 200);
    }else if(condition == 'hapus'){
        setTimeout(() => {
            closeForm('hapus');
            divHapusUser.querySelector('#btnHapusUser').onclick = function(){
                hapusUser(id_event,numRow);
            }
            divHapusUser.style.display = 'block';
        }, 200);
    }
}
closeForm = function(condition){
    if(condition == 'tambah'){
        setTimeout(() => {
            divTambahUser.style.display = 'none';
        }, 200);
    }else if(condition == 'edit'){
        setTimeout(() => {
            editUserForm.reset();
            divEditUser.style.display = 'none';
        }, 200);
    }else if(condition == 'hapus'){
        setTimeout(() => {
            divHapusUser.querySelector('#btnHapusUser').onclick = function(){
                hapusUser();
            }
            divHapusUser.style.display = 'none';
        }, 200);
    }
}
const dataUpload = {
    url:'/users/upload',
    maxFileSize:'10MB'
}
tambahUserForm.onsubmit = function(event){
    console.log('tambah eventt');
    event.preventDefault();
    const namaUser = inpNamaUser.value;
    const deskripsiUser = inpDeskripsiUser.value;
    const kategoriUser = inpKategoriUser.value;
    const tanggalAwal = inpTAwalUser.value;
    const tanggalAkhir = inpTAkhirUser.value;
    const pendaftaranUser = inpPendaftaranUser.value;
    const selectedDateAWal = new Date(tanggalAwal);
    const selectedDateAkhir = new Date(tanggalAkhir);
    if (namaUser.trim() === '') {
        showRedPopup('nama event harus diisi !');
        return;
    }
    if (kategoriUser.trim() === '') {
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
        nama_event:namaUser,
        deskripsi:deskripsiUser,
        kategori:kategoriUser,
        tanggal_awal:formattedTimeAwal+" "+tanggalIAwal,
        tanggal_akhir:formattedTimeAkhir+" "+tanggalIAkhir,
        link:pendaftaranUser.value,
    };
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "/users/admin/tambah");
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                tambahUserForm.reset();
                inpTAwalUser.value = currentDate.toISOString().substring(0, 16);
                inpTAkhirUser.value = currentDate.toISOString().substring(0, 16);
                var response = JSON.parse(xhr.responseText);
                //tambah data ke table event
                const dataTable = ['nama_event','tanggal_awal','tanggal_akhir'];
                var newRow = document.createElement('tr');
                var nCell = document.createElement('th');
                nCell.setAttribute('scope','row');
                //add number row
                var numRow = tableUser.getElementsByTagName('tbody')[0].rows.length+1;
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
                tableUser.querySelector('tbody').appendChild(newRow);
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
                dataUsers.push(requestBody);
                console.log(dataUsers);
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
editUserForm.onsubmit = function(event){
    console.log('ubah eventt');
    event.preventDefault();
    const IDUser = editUserForm.querySelector('#IDUser').value;
    var [id_event, numRow] = IDUser.split(',');
    const namaUser = inpENamaUser.value;
    const deskripsiUser = inpEDeskripsiUser.value;
    const kategoriUser = inpEKategoriUser.value;
    const tanggalAwal = inpETAwalUser.value;
    const tanggalAkhir = inpETAkhirUser.value;
    const pendaftaranUser = inpEPendaftaranUser.value;
    const selectedDateAWal = new Date(tanggalAwal);
    const selectedDateAkhir = new Date(tanggalAkhir);
    if (namaUser.trim() === '') {
        showRedPopup('nama event harus diisi !');
        return;
    }
    if (kategoriUser.trim() === '') {
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
        nama_event:namaUser,
        deskripsi_event:deskripsiUser,
        kategori_event:kategoriUser,
        tanggal_awal_event:formattedTimeAwal+" "+tanggalIAwal,
        tanggal_akhir_event:formattedTimeAkhir+" "+tanggalIAkhir,
        link_pendaftaran:pendaftaranUser.value,
    };
    var xhr = new XMLHttpRequest();
    xhr.open('PUT', "/users/admin/edit");
    // xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                var response = JSON.parse(xhr.responseText);
                //edit data di event array
                var foundUser = dataUsers.find(function(event){  
                    return event.id_event === id_event;
                });
                if (foundUser) {
                    delete requestBody['id_user'];
                    delete requestBody['id_event'];
                    Object.assign(foundUser,requestBody);
                }
                //ubah tabel
                numRow -= 1;
                var tbody = tableUser.getElementsByTagName('tbody')[0];
                var rows = tbody.getElementsByTagName('tr');
                if (numRow >= 0 && numRow < rows.length) {
                    console.log('ganti data event');
                    var cells = rows[numRow].getElementsByTagName('td');
                        var dataTable = [namaUser,formattedTimeAwal+" "+tanggalIAwal,formattedTimeAkhir+" "+tanggalIAkhir];
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
function hapusUser(id_event, numRow){
    console.log('hapus eventt');
    showLoading();
    var requestBody = {
        id_user: idUser,
        id_event: id_event,
    };
    var xhr = new XMLHttpRequest();
    xhr.open('DELETE', "/users/admin/delete");
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify(requestBody));
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                closeLoading();
                closeForm('hapus');
                var response = JSON.parse(xhr.responseText);
                if (numRow >= 1 && numRow <= tableUser.getElementsByTagName('tbody')[0].rows.length){
                    tableUser.querySelector('tbody').removeChild(tableUser.getElementsByTagName('tbody')[0].rows[numRow-1]);
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