<?php
require __DIR__ . '/../Common/init_page.php';
include_once __DIR__ . '/../Common/head.php';
?>
<div class="container-fluid">
    <div class="row justify-content-xl-center">
        <div class="col-xl-6 mb-3">
            <div id="readme-md">
                <div class="alert alert-danger">
                    Error: Unable to load markdown file.
                </div>
            </div>

            <div id="changelog-md">
                <div class="alert alert-danger">
                    Error: Unable to load markdown file.
                </div>
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
$script .= 'document.getElementById("installation").remove();';
$script .= 'document.getElementById("changelog").remove();';
$script .= 'var h2 = document.getElementById("readme-md").getElementsByTagName("h2");';
$script .= 'for (var i = 0, len = h2.length; i < len; ++i) {
        h2[i].after(document.createElement("hr"));
        }';
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
$script .= 'var h2 = document.getElementById("changelog-md").getElementsByTagName("h2");';
$script .= 'for (var i = 0, len = h2.length; i < len; ++i) {
        h2[i].after(document.createElement("hr"));
        }';
$script .= '</script>';
include_once __DIR__ . '/../Common/foot.php';
