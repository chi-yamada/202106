<?php
//セッションを開始
session_start();
//エスケープ処理やデータチェックを行う関数のファイルの読み込み
require "../libs/functions.php";
//POSTされたデータをチェック
$_POST = checkInput( $_POST );
//固定トークンを確認（CSRF対策）
if ( isset( $_POST[ 'ticket' ], $_SESSION[ 'ticket' ] ) ) {
  $ticket = $_POST[ 'ticket' ];
  if ( $ticket !== $_SESSION[ 'ticket' ] ) {
    //トークンが一致しない場合は処理を中止
    die( 'Access Denied!' );
  }
} else {
  //トークンが存在しない場合は処理を中止（直接このページにアクセスするとエラーになる）
  die( 'Access Denied（直接このページにはアクセスできません）' );
}
//POSTされたデータを変数に格納
$name = isset( $_POST[ 'name' ] ) ? $_POST[ 'name' ] : NULL;
$email = isset( $_POST[ 'email' ] ) ? $_POST[ 'email' ] : NULL;
$email_check = isset( $_POST[ 'email_check' ] ) ? $_POST[ 'email_check' ] : NULL;
$tel = isset( $_POST[ 'tel' ] ) ? $_POST[ 'tel' ] : NULL;
$subject = isset( $_POST[ 'subject' ] ) ? $_POST[ 'subject' ] : NULL;
$body = isset( $_POST[ 'body' ] ) ? $_POST[ 'body' ] : NULL;
//POSTされたデータを整形（前後にあるホワイトスペースを削除）
$name = trim( $name );
$email = trim( $email );
$email_check = trim( $email_check );
$tel = trim( $tel );
$subject = trim( $subject );
$body = trim( $body );
//エラーメッセージを保存する配列の初期化
$error = array();
//値の検証（入力内容が条件を満たさない場合はエラーメッセージを配列 $error に設定）
if ( $name == '' ) {
  $error[ 'name' ] = '*お名前は必須項目です。';
  //制御文字でないことと文字数をチェック
} else if ( preg_match( '/\A[[:^cntrl:]]{1,30}\z/u', $name ) == 0 ) {
  $error[ 'name' ] = '*お名前は30文字以内でお願いします。';
}
if ( $email == '' ) {
  $error[ 'email' ] = '*メールアドレスは必須です。';
} else { //メールアドレスを正規表現でチェック
  $pattern = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/uiD';
  if ( !preg_match( $pattern, $email ) ) {
    $error[ 'email' ] = '*メールアドレスの形式が正しくありません。';
  }
}
if ( $email_check == '' ) {
  $error[ 'email_check' ] = '*確認用メールアドレスは必須です。';
} else { //メールアドレスを正規表現でチェック
  if ( $email_check !== $email ) {
    $error[ 'email_check' ] = '*メールアドレスが一致しません。';
  }
}
if ( preg_match( '/\A[[:^cntrl:]]{0,30}\z/u', $tel ) == 0 ) {
  $error[ 'tel' ] = '*電話番号は30文字以内でお願いします。';
}
if ( $tel != '' && preg_match( '/\A\(?\d{2,5}\)?[-(\.\s]{0,2}\d{1,4}[-)\.\s]{0,2}\d{3,4}\z/u', $tel ) == 0 ) {
  $error[ 'tel_format' ] = '*電話番号の形式が正しくありません。';
}
if ( $subject == '' ) {
  $error[ 'subject' ] = '*件名は必須項目です。';
  //制御文字でないことと文字数をチェック
} else if ( preg_match( '/\A[[:^cntrl:]]{1,100}\z/u', $subject ) == 0 ) {
  $error[ 'subject' ] = '*件名は100文字以内でお願いします。';
}
if ( $body == '' ) {
  $error[ 'body' ] = '*内容は必須項目です。';
  //制御文字（タブ、復帰、改行を除く）でないことと文字数をチェック
} else if ( preg_match( '/\A[\r\n\t[:^cntrl:]]{1,1050}\z/u', $body ) == 0 ) {
  $error[ 'body' ] = '*内容は1000文字以内でお願いします。';
}
//POSTされたデータとエラーの配列をセッション変数に保存
$_SESSION[ 'name' ] = $name;
$_SESSION[ 'email' ] = $email;
$_SESSION[ 'email_check' ] = $email_check;
$_SESSION[ 'tel' ] = $tel;
$_SESSION[ 'subject' ] = $subject;
$_SESSION[ 'body' ] = $body;
$_SESSION[ 'error' ] = $error;
//チェックの結果にエラーがある場合は入力フォームに戻す
if ( count( $error ) > 0 ) {
  //エラーがある場合
  $dirname = dirname( $_SERVER[ 'SCRIPT_NAME' ] );
  $dirname = $dirname == DIRECTORY_SEPARATOR ? '' : $dirname;
  $url = ( empty( $_SERVER[ 'HTTPS' ] ) ? 'http://' : 'https://' ) . $_SERVER[ 'SERVER_NAME' ] . $dirname . '/contact.php';
  header( 'HTTP/1.1 303 See Other' );
  header( 'location: ' . $url );
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="../img/favicon.ico">
  <title>お問い合わせ(確認)</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="../carousel.css" rel="stylesheet">
  <link href="../style.css" rel="stylesheet">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  
  <script>
  jQuery('a[href^=#]').click(function() {
    var speed = 1000;
    var href= jQuery(this).attr("href");
    var target = jQuery(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top-headerHight;
    jQuery('body,html').animate({scrollTop:position}, speed, 'swing');
    return false;
  });
  </script>
</head>
<body>
  <header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="../index.html"><img src="../img/logo.gif" width="28px" height="28px">&ensp;大亜電子株式会社</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav me-auto mb-2 mb-md-0">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="../index.html#PDF">PDF</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../index.html#company_info">会社概要</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../index.html#business_content">事業内容</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  
  <div class="container" style="padding-bottom: 80px;">
    <h2>お問い合わせ確認画面</h2>
    <p>以下の内容でよろしければ「送信する」をクリックしてください。<br>
      内容を変更する場合は「戻る」をクリックして入力画面にお戻りください。</p>
      <div class="table-responsive confirm_table">
        <table class="table table-bordered">
          <caption>ご入力内容</caption>
          <tr>
            <th>お名前</th>
            <td><?php echo h($name); ?></td>
          </tr>
          <tr>
            <th>Email</th>
            <td><?php echo h($email); ?></td>
          </tr>
          <tr>
            <th>お電話番号</th>
            <td><?php echo h($tel); ?></td>
          </tr>
          <tr>
            <th>件名</th>
            <td><?php echo h($subject); ?></td>
          </tr>
          <tr>
            <th>お問い合わせ内容</th>
            <td><?php echo nl2br(h($body)); ?></td>
          </tr>
        </table>
      </div>
      <form action="contact.php" method="post" class="confirm">
        <button type="submit" class="btn btn-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0v-2z"/>
          <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
        </svg>
        &nbsp;戻る</button>
      </form>
      <form action="complete.php" method="post" class="confirm">
        <!-- 完了ページへ渡すトークンの隠しフィールド -->
        <input type="hidden" name="ticket" value="<?php echo h($ticket); ?>">
        <button type="submit" class="btn btn-success">
          送信する&nbsp;<svg id="Outline" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 40 40">
            <defs><style>
              .cls-1 {
                fill: none;
              }
              </style></defs>
            <g>
              <path d="M35.93878,4.68567a.94038.94038,0,0,0-.03467-.096.89909.89909,0,0,0-.493-.49328.966.966,0,0,0-.09906-.03571.98591.98591,0,0,0-.24737-.04816.95822.95822,0,0,0-.09973-.00573.98955.98955,0,0,0-.33606.06445l-.00464.00183-.00086.00037L4.62891,16.07129a1.00036,1.00036,0,0,0,.01171,1.8623l12.58594,4.84034,4.83985,12.585A1.00066,1.00066,0,0,0,22.99316,36H23a1.00173,1.00173,0,0,0,.92871-.62842l12-30a.98815.98815,0,0,0,.06476-.338c.0011-.03265-.00354-.06354-.00562-.09613A.98614.98614,0,0,0,35.93878,4.68567Zm-5.09051,3.052L17.75281,20.83313,7.73828,16.98193Zm-7.82972,24.524-3.8518-10.01441L32.26239,9.15167Z"/>
              <rect class="cls-1" width="40" height="40"/>
            </g>
          </svg></button>
      </form>
    </div>
    <footer style="position: absolute; bottom: 0;">
      <p>Copyright (C) 2007-2021 dia-electron Co., Ltd. All Rights Reserved.</p>
    </footer>
  </body>
</html>