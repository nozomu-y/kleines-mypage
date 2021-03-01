<?php
require __DIR__ . '/../Common/init_page.php';
include_once __DIR__ . '/../Common/head.php';
?>

<div class="d-none d-md-block">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6 mb-3">
                <div class="card mb-3">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">README.md</h6>
                    </div>
                    <div class="card-body">
                        <div class="markdown-body" id="readme-md">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-3">
                <div class="card mb-3">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">CHANGELOG.md</h6>
                    </div>
                    <div class="card-body">
                        <div class="markdown-body" id="changelog-md">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-block d-md-none">
    <div class="card card-flush mb-3">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">README.md</h6>
        </div>
        <div class="card-body">
            <div class="markdown-body" id="readme-md-sm">
            </div>
        </div>
    </div>
    <div class="card card-flush mb-3">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">CHANGELOG.md</h6>
        </div>
        <div class="card-body">
            <div class="markdown-body" id="changelog-md-sm">
            </div>
        </div>
    </div>
</div>

<?php
$script .= '<script src="' . MYPAGE_ROOT . '/Resources/js/marked.min.js' . '"></script>';

$readme_md = file_get_contents("../README.md");
$readme_md = str_replace("\n", '\\n', $readme_md);
$readme_md = str_replace("\"", "\\\"", $readme_md);

$script .= '<script>';
$script .= 'var markdown = "' . $readme_md . '";';
$script .= 'var html = marked(markdown);$("#readme-md").html(html);';
$script .= 'document.getElementById("readme-md").getElementsByTagName("h1")[0].innerHTML = "<a href=\"https://github.com/nozomu-y/kleines-mypage\">Kleines Mypage</a>";';
$script .= 'var link = document.getElementById("readme-md").getElementsByTagName("a");
        for (var i = 0, len = link.length; i < len; ++i) {
            link[i].setAttribute("target", "_blank");
        }';
$script .= 'document.getElementById("readme-md").getElementById("installation").remove();';
$script .= 'document.getElementById("readme-md").getElementById("changelog").remove();';
$script .= '</script>';

$script .= '<script>';
$script .= 'var markdown = "' . $readme_md . '";';
$script .= 'var html = marked(markdown);$("#readme-md-sm").html(html);';
$script .= 'document.getElementById("readme-md-sm").getElementsByTagName("h1")[0].innerHTML = "<a href=\"https://github.com/nozomu-y/kleines-mypage\">Kleines Mypage</a>";';
$script .= 'var link = document.getElementById("readme-md-sm").getElementsByTagName("a");
        for (var i = 0, len = link.length; i < len; ++i) {
            link[i].setAttribute("target", "_blank");
        }';
$script .= 'document.getElementById("readme-md-sm").getElementById("installation").remove();';
$script .= 'document.getElementById("readme-md-sm").getElementById("changelog").remove();';
$script .= '</script>';

$changelog_md = file_get_contents("../CHANGELOG.md");
$changelog_md = str_replace("\n", '\\n', $changelog_md);
$changelog_md = str_replace("\"", "\\\"", $changelog_md);

$script .= '<script>';
$script .= 'var markdown = "' . $changelog_md . '";';
$script .= 'var html = marked(markdown);$("#changelog-md").html(html);';
$script .= 'var link = document.getElementById("changelog-md").getElementsByTagName("a");
        for (var i = 0, len = link.length; i < len; ++i) {
            link[i].setAttribute("target", "_blank");
        }';
$script .= '
        for (var i = 4; i >= 1; i--) {
            $("#changelog-md h"+String(i)).replaceWith(function() {
                return "<h"+String(i+1)+">" + $(this).html() + "</h"+String(i+1)+">";
            });
        }';
$script .= 'document.getElementById("changelog-md").getElementsByTagName("h2")[0].remove();';
$script .= '</script>';

$script .= '<script>';
$script .= 'var markdown = "' . $changelog_md . '";';
$script .= 'var html = marked(markdown);$("#changelog-md-sm").html(html);';
$script .= 'var link = document.getElementById("changelog-md-sm").getElementsByTagName("a");
        for (var i = 0, len = link.length; i < len; ++i) {
            link[i].setAttribute("target", "_blank");
        }';
$script .= '
        for (var i = 4; i >= 1; i--) {
            $("#changelog-md-sm h"+String(i)).replaceWith(function() {
                return "<h"+String(i+1)+">" + $(this).html() + "</h"+String(i+1)+">";
            });
        }';
$script .= 'document.getElementById("changelog-md-sm").getElementsByTagName("h2")[0].remove();';
$script .= '</script>';
include_once __DIR__ . '/../Common/foot.php';
