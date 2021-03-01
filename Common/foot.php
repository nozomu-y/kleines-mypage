</div>
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <?php
        if ($USER->isMaster()) {
        ?>
            <div class="copyright text-center mb-1">
                <span>Current branch is <i class="fas fa-code-branch"></i> <?= getGitBranch() ?></span>
            </div>
        <?php
        }
        ?>
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Chor Kleines <?= date("Y") ?></span>
        </div>
    </div>
</footer>
</div>
</div>
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Bootstrap core JavaScript-->
<script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.min.js?cache=<?= $CSS_JS_CACHE ?>"></script>
<script src="<?= MYPAGE_ROOT ?>/Resources/js/bootstrap.bundle.min.js?cache=<?= $CSS_JS_CACHE ?>"></script>

<!-- Core plugin JavaScript-->
<script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.easing.min.js?cache=<?= $CSS_JS_CACHE ?>"></script>

<!-- Custom scripts for all pages-->
<script src="<?= MYPAGE_ROOT ?>/Resources/js/sb-admin-2.min.js?cache=<?= $CSS_JS_CACHE ?>"></script>
<script src="<?= MYPAGE_ROOT ?>/Resources/js/Chart.min.js?cache=<?= $CSS_JS_CACHE ?>"></script>
<script src="<?= MYPAGE_ROOT ?>/Resources/js/tagsinput.min.js?cache=<?= $CSS_JS_CACHE ?>"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-flash-1.6.1/b-html5-1.6.1/datatables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.20/sorting/currency.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0/plugins/autoloader/prism-autoloader.min.js" integrity="sha512-zc7WDnCM3aom2EziyDIRAtQg1mVXLdILE09Bo+aE1xk0AM2c2cVLfSW9NrxE5tKTX44WBY0Z2HClZ05ur9vB6A==" crossorigin="anonymous"></script>
<?php
echo $script;
?>

<script>
    var markdowns = document.getElementsByClassName("markdown-body");
    for (var i = 0; i < markdowns.length; i++) {
        var links = markdowns[i].getElementsByTagName("a");
        for (var j = 0; j < links.length; j++) {
            if (!links[j].href.includes('<?= WEB_DOMAIN . MYPAGE_ROOT ?>')) {
                links[j].target = "_blank";
            }
        }
    }
</script>

</body>

</html>