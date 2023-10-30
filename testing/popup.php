<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>popup</title>
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Vendor CSS Files -->
    <link href="/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal"> Basic Modal </button> -->
    <button type="button" class="btn btn-primary" onclick="openDelete()"> Basic Modal </button>
    <div class="modal fade" id="basicModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi hapus Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus Admin ?  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('device.destroy', $e->id_device) }}" id="deleteForm" method="POST">
                        <input type="hidden">
                        <button type="submit" class="btn btn-success">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="modal fade" id="basicModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Basic Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Non omnis incidunt qui sed occaecati magni asperiores est mollitia. Soluta at et reprehenderit.
                    Placeat autem numquam et fuga numquam. Tempora in facere consequatur sit dolor ipsum. Consequatur
                    nemo amet incidunt est facilis. Dolorem neque recusandae quo sit molestias sint dignissimos.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div> -->
    <script>
        var modal = document.getElementById('basicModal');
        function openDelete(){
            var myModal = new bootstrap.Modal(modal);
            myModal.show();
        }
    </script>
    <!-- Vendor JS Files -->
    <script src="/public/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/vendor/chart.js/chart.umd.js"></script>
    <script src="/public/assets/vendor/echarts/echarts.min.js"></script>
    <script src="/public/assets/vendor/quill/quill.min.js"></script>
    <script src="/public/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/public/assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="/public/assets/vendor/php-email-form/validate.js"></script>
    <script src="/public/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="/public/assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>