<?php
	session_start();
	require_once($_SERVER['DOCUMENT_ROOT'].'/TicketSystems/mypage/TicketSystems/config/config.php');
	require_once(ROOT.'/controller/functions.php');
  if (isset($_SESSION["mypage_email"])) {	//ログインしていた場合、トップページへ
    header("Location: ".SERVER."/view/index.php");
    exit();
	}

	//最年長・最年少学年を取得(8年分)
	$newestGrade = date("y");	//1年生
	$oldestGrade = $newestGrade - 7;	//最上級生

	require_once(ROOT.'/view/header.php');
	getHeader("サインイン","everyone");

	if(isset($_SESSION['mypage_auth_error'])){
		echo "<p>サインインに失敗しました。エラーコード：";
		echo $_SESSION['mypage_auth_error'];
		echo "</p><br>";
		unset($_SESSION['mypage_auth_error']);
	}
	if(isset($_SESSION['mypage_register_error'])){
		echo "<p>サインアップに失敗しました。エラーコード:".$_SESSION['mypage_register_error']."</p><br>";
		unset($_SESSION['mypage_register_error']);
	}
	if(strcmp($_GET['status'],"succeedSignUp")==0){
		echo "<p>サインアップに成功しました。続いてサインインしてください。</p>";
	}
	if(strcmp($_SESSION['tp_status'],"succeedSignIn")==0){
		unset($_SESSION['tp_status']);
		echo "<p>サインインに成功しました。</p>";
		echo "<p><a href=\"".SERVER."/view/everyone/index.php\">トップページへ</a></p>";
	}

	if(isset($_SESSION['tp_secret'])){
		unset($_SESSION['tp_secret']);
	}
?>
<h3>秘密のパスワードによる初期設定(ログイン後)</h3>
<p><a href="<?=SERVER?>/view/secret/index.php">こちらから</a></p>
<br>
<h2>マイページログイン</h2>
<br>
<form action="<?=SERVER?>/model/signInHandler.php" method="post" class="needs-validation" novalidate>
	<div class="form-row">
		<div class="form-group col-3">
			<p class="componentsTitle">email</p>
		</div>
		<div class="form-group col-9 has-feedback">
			<input class="form-control" type="email" name="email" required>
		</div>
		<div class="invalid-feedback">入力してください</div>
	</div>
	<div class="form-row">
		<div class="form-group col-3">
			<p class="componentsTitle">password</p>
		</div>
		<div class="form-group col-9 has-feedback">
			<input class="form-control" type="password" name="password" required>
		</div>
		<div class="invalid-feedback">入力してください</div>
	</div>
	<button type="submit" class="btn btn-primary">Sign In!</button>
</form>
<br>
<h2>メンバー登録はこちら</h2>
<form action="<?=SERVER?>/model/signUpHandler.php" method="post" class="needs-validation" novalidate>
	<div class="form-group has-feedback">
		<p class="componentsTitle">学年</p>
		<?php for($grd=$newestGrade;$grd>=$oldestGrade;$grd--): ?>
		<div class="custom-control custom-radio">
			<input type="radio" class="custom-control-input" name="grade" value="<?=$grd?>" required>
			<label class="custom-control-label" for="<?=$grd?>"><?=$grd?></label>
		</div>
		<?php endfor; ?>
		<div class="invalid-feedback">選択してください</div>
	</div>
	<div class="form-group has-feedback">
		<p class="componentsTitle">パート</p>
		<div class="custom-control custom-radio">
			<input type="radio" class="custom-control-input" name="grade" value="S" required>
			<label class="custom-control-label" for="S">Sop</label>
		</div>
		<div class="custom-control custom-radio">
			<input type="radio" class="custom-control-input" name="grade" value="A" required>
			<label class="custom-control-label" for="A">Alt</label>
		</div>
		<div class="custom-control custom-radio">
			<input type="radio" class="custom-control-input" name="grade" value="T" required>
			<label class="custom-control-label" for="T">Ten</label>
		</div>
		<div class="custom-control custom-radio">
			<input type="radio" class="custom-control-input" name="grade" value="B" required>
			<label class="custom-control-label" for="B">Bas</label>
		</div>
		<div class="invalid-feedback">パートを選択してください</div>
	</div>
	<div class="form-row">
		<div class="form-group col-6">
			<p class="componentsTitle">姓</p>
			<input class="form-control" type="text" name="last_name" required>
		</div>
		<div class="invalid-feedback">入力してください</div>
		<div class="form-group col-6">
			<p class="componentsTitle">名</p>
			<input class="form-control" type="text" name="first_name" required>
		</div>
		<div class="invalid-feedback">入力してください</div>
	</div>
	<div class="form-row">
		<div class="form-group col-3">
			<p class="componentsTitle">email</p>
		</div>
		<div class="form-group col-9">
			<input class="form-control" type="email" name="email" required>
		</div>
		<div class="invalid-feedback">入力してください</div>
	</div>
	<div class="form-row">
		<div class="form-group col-3">
			<p class="componentsTitle">password</p>
		</div>
		<div class="form-group col-9">
			<input class="form-control" type="password" name="password" required>
		</div>
		<div class="invalid-feedback">入力してください</div>
	</div>
	<div class="form-row">
		<div class="form-group col-3">
			<p class="componentsTitle">password(確認用)</p>
		</div>
		<div class="form-group col-9">
			<input class="form-control" type="password" name="password-confirm" required>
		</div>
		<div class="invalid-feedback">入力してください</div>
	</div>
	<button type="submit" class="btn btn-primary">Sign Up!</button>
</form>
<script type="text/javascript" src="<?=SERVER?>/view/js/formValidation.js"></script>
<!-- <script type="text/javascript" src="<?=SERVER?>/view/js/passwordConfirm.js"></script>  -->

<?php
	require_once(ROOT.'/view/footer.php');
	
?>