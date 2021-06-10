<?php
//セッションを開始
session_start();
//エスケープ処理やデータをチェックする関数を記述したファイルの読み込み
require '../libs/functions.php';
//メールアドレス等を記述したファイルの読み込み
require '../libs/mailvars.php';

//お問い合わせ日時を日本時間に
date_default_timezone_set('Asia/Tokyo');

//POSTされたデータをチェック
$_POST = checkInput( $_POST );
//固定トークンを確認（CSRF対策）
if ( isset( $_POST[ 'ticket' ], $_SESSION[ 'ticket' ] ) ) {
  $ticket = $_POST[ 'ticket' ];
  if ( $ticket !== $_SESSION[ 'ticket' ] ) {
    //トークンが一致しない場合は処理を中止
    die( 'Access denied' );
  }
} else {
  //トークンが存在しない場合（入力ページにリダイレクト）
  //die( 'Access Denied（直接このページにはアクセスできません）' );  //処理を中止する場合
  $dirname = dirname( $_SERVER[ 'SCRIPT_NAME' ] );
  $dirname = $dirname == DIRECTORY_SEPARATOR ? '' : $dirname;
  $url = ( empty( $_SERVER[ 'HTTPS' ] ) ? 'http://' : 'https://' ) . $_SERVER[ 'SERVER_NAME' ] . $dirname . '/contact.php';
  header( 'HTTP/1.1 303 See Other' );
  header( 'location: ' . $url );
  exit; //忘れないように
}

//変数にエスケープ処理したセッション変数の値を代入
$name = h( $_SESSION[ 'name' ] );
$email = h( $_SESSION[ 'email' ] ) ;
$tel =  h( $_SESSION[ 'tel' ] ) ;
$subject = h( $_SESSION[ 'subject' ] );
$body = h( $_SESSION[ 'body' ] );

//メール本文の組み立て
$mail_body = 'HPからのお問い合わせ' . "\n\n";
$mail_body .=  date("Y年m月d日 H時i分") . "\n\n";
$mail_body .=  "名前： " .$name . "\n";
$mail_body .=  "Email： " . $email . "\n"  ;
$mail_body .=  "電話番号： " . $tel . "\n\n" ;
$mail_body .=  "＜お問い合わせ内容＞" . "\n" . $subject . "\n" . $body;

//-------- sendmail（mb_send_mail）を使ったメールの送信処理------------

//メールの宛先（名前<メールアドレス> の形式）。値は mailvars.php に記載
$mailTo = mb_encode_mimeheader(MAIL_TO_NAME) ."<" . MAIL_TO. ">";

//Return-Pathに指定するメールアドレス
$returnMail = MAIL_RETURN_PATH; //
//mbstringの日本語設定
mb_language( 'ja' );
mb_internal_encoding( 'UTF-8' );

// 送信者情報（From ヘッダー）の設定
$header = "From: " . mb_encode_mimeheader($name) ."<" . $email. ">\n";
//$header .= "Cc: " . mb_encode_mimeheader(MAIL_CC_NAME) ."<" . MAIL_CC.">\n";
//$header .= "Bcc: <" . MAIL_BCC.">";

//メールの送信（結果を変数 $result に格納）
if ( ini_get( 'safe_mode' ) ) {
  //セーフモードがOnの場合は第5引数が使えない
  $result = mb_send_mail( $mailTo, $subject, $mail_body, $header );
} else {
  $result = mb_send_mail( $mailTo, $subject, $mail_body, $header, '-f' . $returnMail );
}

//メール送信の結果判定
if ( $result ) {
  //成功した場合はセッションを破棄
  $_SESSION = array(); //空の配列を代入し、すべてのセッション変数を消去
  session_destroy(); //セッションを破棄
} else {
  //送信失敗時（もしあれば）
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="../img/favicon.ico">
  <title>お問い合わせ(結果)</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="../carousel.css" rel="stylesheet">
  <link href="../style.css" rel="stylesheet">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <link href="../bootstrap.min.css" rel="stylesheet">
  <body>
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
    <div class="container">
      <?php if ( $result ): ?>
        <h3>送信が完了しました</h3>
        <p>お問い合わせいただきありがとうございました。</p>
      <?php else: ?>
        <p>送信に失敗しました。</p>
        <p>ご迷惑をおかけしますが,<br>
          しばらくしてもう一度お試しになるか、メールにてご連絡ください。</p>
        <?php endif; ?>
        <div class="confirm">
          <p><a class="btn btn-secondary" href="../index.html" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6zm5-.793V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
            <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
          </svg>
          &nbsp;ホームに戻る</a></p>
        </div>
      </div>
      <footer style="position: absolute; bottom: 0;">
        <p>Copyright (C) 2007-2021 dia-electron Co., Ltd. All Rights Reserved.</p>
      </footer>
    </body>
  </html>
