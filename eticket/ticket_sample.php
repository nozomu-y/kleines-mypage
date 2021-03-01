<?php
require(__DIR__ . '/../Core/config.php');
?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= "Kleines Mypage" . $PAGE_NAME ?></title>

    <!-- Fonts -->
    <link href="https://use.fontawesome.com/releases/v5.12.0/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP:400,500&display=swap&subset=japanese" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/sb-admin-2.min.css">
    <link rel="stylesheet" href="<?= MYPAGE_ROOT ?>/Resources/css/style.css">
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/html2canvas.min.js"></script>
    <style>
        .qrcode img {
            width: 200px;
            margin: auto;
        }

        .table-sm td,
        .table-sm th {
            padding: .1rem;
        }

        hide {
            display: none !important;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown no-arrow">
                            <span class="text-primary">Chor Kleines eticket</span>
                        </li>
                    </ul>
                </nav>

                <div class="container my-4">
                    <div class="d-flex justify-content-center my-5" id="spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id="ticket-canvas" class="mb-3 text-center"></div>
                    <div class="text-center">
                        <a class="btn btn-primary disabled" download="ticket.png" id="ticket-image-link" href>チケットをダウンロード</a>
                    </div>
                </div>

                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Chor Kleines <?= date("Y") ?></span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Used for Ticket Rendering (cannot be seen) -->
    <div class="card mb-3 mx-auto" style="width: 400px; top: -10000px;" id="ticket">
        <img class="card-img-top" src="<?= $base64 ?>">
        <!-- <div class="qrcode mt-3" id="qrcode"></div> -->
        <div class="qrcode mt-3 text-center" id="qrcode-svg"></div>
        <div class="card-body">
            <h2 class="h4 text-center text-dark">東京工業大学混声合唱団<br>コール・クライネス</h2>
            <h3 class="h5 text-center text-dark">第55回演奏会</h3>
            <table class="table table-sm table-borderless my-3">
                <tbody>
                    <tr>
                        <th class="pr-3 text-nowrap" style="width: 70px;">日付</th>
                        <td>2021年1月15日</td>
                    </tr>
                    <tr>
                        <th class="pr-3 text-nowrap" style="width: 70px;">ホール</th>
                        <td>ミューザ川崎シンフォニーホール</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table class="table table-sm table-borderless mb-3" style="width: auto !important; margin: auto;">
                <tbody>
                    <tr>
                        <th class="text-nowrap">開場</th>
                        <td class="pr-5">18:00</td>
                        <th class="text-nowrap">座席</th>
                        <td>自由席</td>
                    </tr>
                    <tr>
                        <th class="text-nowrap">開演</th>
                        <td class="pr-5">18:30</td>
                        <th class="text-nowrap">金額</th>
                        <td>￥1,500</td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center">
                <span style="font-family: 'Roboto Mono', monospace;">No.000123</span>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.min.js"></script>
    <!-- <script src="<?= MYPAGE_ROOT ?>/Resources/js/bootstrap.bundle.min.js"></script> -->

    <!-- Core plugin JavaScript-->
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/sb-admin-2.min.js"></script>
    <!-- <script src="<?= MYPAGE_ROOT ?>/Resources/js/qrcode.min.js"></script> -->
    <script src="<?= MYPAGE_ROOT ?>/Resources/js/qrcode-svg.min.js"></script>
    <script>
        var qrcode = new QRCode({
            msg: 'https://www.chorkleines.com/member/mypage/eticket/',
            dim: 256,
            ecl: "H"
        })
        document.getElementById("qrcode-svg").appendChild(qrcode);
    </script>
    <script type="text/javascript">
        // var qrcode = new QRCode(document.getElementById("qrcode"), {
        //     text: "https://www.chorkleines.com/member/mypage/eticket/",
        //     width: 100,
        //     height: 100,
        //     colorDark: "#000000",
        //     colorLight: "#ffffff",
        //     correctLevel: QRCode.CorrectLevel.H
        // });

        window.onload = function() {
            html2canvas(document.querySelector("#ticket"), {
                scale: 5,
                imageTimeout: 0
            }).then(function(canvas) {
                var img = canvas.toDataURL("image/png");
                var link = document.getElementById("ticket-image-link");
                document.getElementById("ticket").remove();
                var ticket_canvas = document.getElementById("ticket-canvas");
                canvas.style.width = "100%";
                canvas.style.height = "auto";
                canvas.style.maxWidth = "400px";
                document.getElementById("spinner").remove();
                ticket_canvas.appendChild(canvas);
                link.setAttribute("href", img);
                link.classList.remove("disabled");
            });
        };
    </script>

</body>

</html>