<?php
ob_start();
session_start();
if (!isset($_SESSION['mypage_email'])) {
    header('Location: /member/mypage/login/');
    exit();
}

require_once('/home/chorkleines/www/member/mypage/Core/dbconnect.php');
$email = $_SESSION['mypage_email'];
$query = "SELECT * FROM members WHERE email='$email'";
$result = $mysqli->query($query);
if (!$result) {
    print('Query Failed : ' . $mysqli->error);
    $mysqli->close();
    exit();
}
$user = new User($result->fetch_assoc());

if (!($user->admin == 1)) {
    header('Location: /member/mypage/');
    exit();
}

include_once('/home/chorkleines/www/member/mypage/Common/head.php');
?>

<div class="container-fluid">
    <h1 class="h3 text-gray-800 mb-4">電子チケット</h1>
    <div class="row">
        <div class=" col-xl-9 col-sm-12">
            <!-- カメラ映像 -->
            <video id="camera" muted autoplay playsinline></video>
            <!-- 処理用 -->
            <canvas id="picture" hidden></canvas>
        </div>
        <div class="col-xl-3 col-sm-12">
        </div>
    </div>
</div>
<form action="test.php" method="POST" name="QRPost">
    <input type="hidden" name="data" value="" />
</form>

<script src="jsQR.min.js"></script>
<script>
    if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
        alert("このブラウザーは未対応です");
        exit();
    }

    const video = document.querySelector("#camera");
    const canvas = document.querySelector("#picture");
    const ctx = canvas.getContext("2d");

    window.onload = () => {
        /** カメラ設定 */
        const constraints = {
            audio: false,
            video: {
                width: 300,
                height: 200,
                facingMode: "environment" // 背面カメラを利用する
                // facingMode: "user" // フロントカメラを利用する
            }
        };

        /** sync camera and <video> tag **/
        navigator.mediaDevices
            .getUserMedia(constraints)
            .then(function(stream) {
                video.srcObject = stream;
                video.onloadedmetadata = function(e) {
                    video.play();
                    checkPicture();
                };
            })
            .catch(function(err) {
                console.log(err.name + ": " + err.message);
            });
    };

    /*
     * QRコードの読み取り
     */
    function checkPicture() {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, canvas.width, canvas.height);

        // QRコードが存在する場合
        if (code) {
            sendQRResult(code.data);
        } else {
            setTimeout(
                function() {
                    checkPicture();
                }.bind(this),
                300
            );
        }
    }

    function sendQRResult(data) {
        var f = document.forms["QRPost"];
        f.data.value = data;
        f.submit();
    }
</script>


<?php
include_once('/home/chorkleines/www/member/mypage/Common/foot.php');
