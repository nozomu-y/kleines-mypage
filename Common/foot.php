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
            <span>Copyright &copy; Chor Kleines <?php echo date("Y") ?></span>
        </div>
    </div>
</footer>
</div>
</div>
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<script>
    function sidebarToggle() {
        if (document.getElementById('accordionSidebar').classList.contains('toggled')) {
            document.cookie = 'MypageSidebarToggle=;path=<?= MYPAGE_ROOT ?>';
        } else {
            document.cookie = 'MypageSidebarToggle=toggled;path=<?= MYPAGE_ROOT ?>';
        }
    }
    for (var c of document.cookie.split(";")) {
        var cArray = c.split('=');
        if (cArray[0] == 'MypageSidebarToggle') {
            if (cArray[1] == 'toggled') {
                document.getElementById('accordionSidebar').classList.add('toggled');
            } else {
                document.getElementById('accordionSidebar').classList.remove('toggled');
            }
        }
    }
</script>

<!-- Bootstrap core JavaScript-->
<script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.min.js"></script>
<script src="<?= MYPAGE_ROOT ?>/Resources/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= MYPAGE_ROOT ?>/Resources/js/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= MYPAGE_ROOT ?>/Resources/js/sb-admin-2.min.js"></script>
<script src="<?= MYPAGE_ROOT ?>/Resources/js/Chart.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-flash-1.6.1/b-html5-1.6.1/datatables.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.20/sorting/currency.js"></script>
<?php
echo $script;
?>
</body>

</html>