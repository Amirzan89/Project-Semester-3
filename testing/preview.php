<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="downloadKTP">
        <h1>download poster event</h1>
        <form action="/preview.php" method="POST">
            <input type="hidden" name="id_user" value="37">
            <input type="hidden" name="item" value="event">
            <input type="hidden" name="deskripsi" value="foto">
            id event
            <input type="text" name="id_event" id=""><br>
            <input type="submit" value="kirim">
        </form>
    </div>
    <div class="downloadKTP">
        <h1>download surat tempat</h1>
        <form action="/preview.php" method="POST">
            <input type="hidden" name="id_user" value="37">
            <input type="hidden" name="item" value="tempat">
            <input type="hidden" name="deskripsi" value="surat">
            id sewa
            <input type="text" name="id_sewa" id=""><br>
            <input type="submit" value="kirim">
        </form>
    </div>
    <br>
    <div class="downloadKTP">
        <h1>download surat pentas</h1>
        <form action="/preview.php" method="POST">
            <input type="hidden" name="id_user" value="37">
            <input type="hidden" name="item" value="pentas">
            <input type="hidden" name="deskripsi" value="ktp">
            id pentas
            <input type="text" name="id_pentas" id=""><br>
            <input type="submit" value="kirim">
        </form>
    </div>
    <br>
    <div class="downloadKTP">
        <h1>download ktp seniman</h1>
        <form action="/preview.php" method="POST">
            <input type="hidden" name="id_user" value="37">
            <input type="hidden" name="item" value="seniman">
            <input type="hidden" name="deskripsi" value="ktp">
            id seniman
            <input type="text" name="id_seniman" id=""><br>
            <input type="submit" value="kirim">
        </form>
    </div>
    <br>
    <div class="downloadKTP">
        <h1>download foto seniman</h1>
        <form action="/preview.php" method="POST">
            <input type="hidden" name="id_user" value="37">
            <input type="hidden" name="item" value="seniman">
            <input type="hidden" name="deskripsi" value="foto">
            id seniman
            <input type="text" name="id_seniman" id=""><br>
            <input type="submit" value="kirim">
        </form>
    </div>
    <br>
    <div class="downloadKTP">
        <h1>download surat seniman</h1>
        <form action="/preview.php" method="POST">
            <input type="hidden" name="id_user" value="37">
            <input type="hidden" name="item" value="seniman">
            <input type="hidden" name="deskripsi" value="surat">
            id seniman
            <input type="text" name="id_seniman" id=""><br>
            <input type="submit" value="kirim">
        </form>
    </div>
</body>
</html>