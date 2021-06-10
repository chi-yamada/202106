<?php
//セッションを開始
session_start();
//セッションIDを更新して変更（セッションハイジャック対策）
session_regenerate_id( TRUE );
//エスケープ処理やデータチェックを行う関数のファイルの読み込み
require '../libs/functions.php';
//初回以外ですでにセッション変数に値が代入されていれば、その値を。そうでなければNULLで初期化
$name = isset( $_SESSION[ 'name' ] ) ? $_SESSION[ 'name' ] : NULL;
$email = isset( $_SESSION[ 'email' ] ) ? $_SESSION[ 'email' ] : NULL;
$email_check = isset( $_SESSION[ 'email_check' ] ) ? $_SESSION[ 'email_check' ] : NULL;
$tel = isset( $_SESSION[ 'tel' ] ) ? $_SESSION[ 'tel' ] : NULL;
$subject = isset( $_SESSION[ 'subject' ] ) ? $_SESSION[ 'subject' ] : NULL;
$body = isset( $_SESSION[ 'body' ] ) ? $_SESSION[ 'body' ] : NULL;
$error = isset( $_SESSION[ 'error' ] ) ? $_SESSION[ 'error' ] : NULL;
//個々のエラーを初期化
$error_name = isset( $error['name'] ) ? $error['name'] : NULL;
$error_email = isset( $error['email'] ) ? $error['email'] : NULL;
$error_email_check = isset( $error['email_check'] ) ? $error['email_check'] : NULL;
$error_tel = isset( $error['tel'] ) ? $error['tel'] : NULL;
$error_tel_format = isset( $error['tel_format'] ) ? $error['tel_format'] : NULL;
$error_subject = isset( $error['subject'] ) ? $error['subject'] : NULL;
$error_body = isset( $error['body'] ) ? $error['body'] : NULL;
//CSRF対策の固定トークンを生成
if ( !isset( $_SESSION[ 'ticket' ] ) ) {
  //セッション変数にトークンを代入
  $_SESSION[ 'ticket' ] = sha1( uniqid( mt_rand(), TRUE ) );
}
//トークンを変数に代入
$ticket = $_SESSION[ 'ticket' ];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Hugo 0.79.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="../img/favicon.ico">
  <title>お問い合わせ</title>
  <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/carousel/">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal-default-theme.min.css">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/remodal/1.0.5/remodal.min.js"></script>
  <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
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
  <link href="../carousel.css" rel="stylesheet">
  <link href="../style.css" rel="stylesheet">
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
    <h2>お問い合わせフォーム</h2>
    <p>以下のフォームからお問い合わせください。</p>
    <form id="main_contact" method="post" action="confirm.php">
      <div class="form-group">
        <label for="name">お名前
          <span class="error"><?php echo h( $error_name ); ?></span>
        </label>
        <input type="text" class="form-control validate max50 required" id="name" name="name" placeholder="必須" value="<?php echo h($name); ?>">
      </div>
      <div class="form-group">
        <label for="email">Email
          <span class="error"><?php echo h( $error_email ); ?></span>
        </label>
        <input type="text" class="form-control validate mail required" id="email" name="email" placeholder="必須" value="<?php echo h($email); ?>">
      </div>
      <div class="form-group">
        <label for="email_check">Email（確認用）
          <span class="error"><?php echo h( $error_email_check ); ?></span>
        </label>
        <input type="text" class="form-control validate email_check required" id="email_check" name="email_check" placeholder="確認のためもう一度ご入力ください。" value="<?php echo h($email_check); ?>">
      </div>
      <div class="form-group">
        <label for="tel">お電話番号（半角英数字）
          <span class="error"><?php echo h( $error_tel ); ?></span>
          <span class="error"><?php echo h( $error_tel_format ); ?></span>
        </label>
        <input type="text" class="validate max30 tel form-control" id="tel" name="tel" value="<?php echo h($tel); ?>" placeholder="">
      </div>
      <div class="form-group">
        <label for="subject">件名
          <span class="error"><?php echo h( $error_subject ); ?></span>
        </label>
        <input type="text" class="form-control validate max100 required" id="subject" name="subject" placeholder="必須" value="<?php echo h($subject); ?>">
      </div>
      <div class="form-group">
        <label for="body">お問い合わせ内容（1000文字まで）
          <span class="error"><?php echo h( $error_body ); ?></span>
        </label>
        <span id="count"> </span>/1000
        <textarea class="form-control validate max1000 required" id="body" name="body" placeholder="必須" rows="3"><?php echo h($body); ?></textarea>
      </div>
      <div class="confirm">
        <p><a class="btn btn-secondary" href="../index.html" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0v-2z"/>
          <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
        </svg>
        &nbsp;戻る</a></p>
      </div>
      <button type="submit" class="btn btn-primary">
        確認画面へ&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
          <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5H3z"/>
          <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
        </svg></button>
        <!--確認ページへトークンをPOSTする、隠しフィールド「ticket」-->
        <input type="hidden" name="ticket" value="<?php echo h($ticket); ?>">
      </form>
    </div>
    <script>
    jQuery(function($){
      
      //エラーを表示する関数（error クラスの p 要素を追加して表示）
      function show_error(message, this$) {
        text = this$.parent().find('label').text() + message;
        this$.parent().append("<p class='error'>" + text + "</p>");
      }
      
      //フォームが送信される際のイベントハンドラの設定
      $("#main_contact").submit(function(){
        //エラー表示の初期化
        $("p.error").remove();
        $("div").removeClass("error");
        var text = "";
        $("#errorDispaly").remove();
        
        //メールアドレスの検証
        var email =  $.trim($("#email").val());
        if(email && !(/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/gi).test(email)){
          $("#email").after("<p class='error'>メールアドレスの形式が異なります</p>");
        }
        //確認用メールアドレスの検証
        var email_check =  $.trim($("#email_check").val());
        if(email_check && email_check != $.trim($("input[name="+$("#email_check").attr("name").replace(/^(.+)_check$/, "$1")+"]").val())){
          show_error("が異なります", $("#email_check"));
        }
        //電話番号の検証
        var tel = $.trim($("#tel").val());
        if(tel && !(/^\(?\d{2,5}\)?[-(\.\s]{0,2}\d{1,4}[-)\.\s]{0,2}\d{3,4}$/gi).test(tel)){
          $("#tel").after("<p class='error'>電話番号の形式が異なります（半角英数字でご入力ください）</p>");
        }
        
        //1行テキスト入力フォームとテキストエリアの検証
        $(":text,textarea").filter(".validate").each(function(){
          //必須項目の検証
          $(this).filter(".required").each(function(){
            if($(this).val()==""){
              show_error(" は必須項目です", $(this));
            }
          });
          //文字数の検証
          $(this).filter(".max30").each(function(){
            if($(this).val().length > 30){
              show_error(" は30文字以内です", $(this));
            }
          });
          $(this).filter(".max50").each(function(){
            if($(this).val().length > 50){
              show_error(" は50文字以内です", $(this));
            }
          });
          $(this).filter(".max100").each(function(){
            if($(this).val().length > 100){
              show_error(" は100文字以内です", $(this));
            }
          });
          //文字数の検証
          $(this).filter(".max1000").each(function(){
            if($(this).val().length > 1000){
              show_error(" は1000文字以内でお願いします", $(this));
            }
          });
        });
        
        //error クラスの追加の処理
        if($("p.error").length > 0){
          $("p.error").parent().addClass("error");
          $('html,body').animate({ scrollTop: $("p.error:first").offset().top-180 }, 'slow');
          return false;
        }
      });
      
      //テキストエリアに入力された文字数を表示
      $("textarea").on('keydown keyup change', function() {
        var count = $(this).val().length;
        $("#count").text(count);
        if(count > 1000) {
          $("#count").css({color: 'red', fontWeight: 'bold'});
        }else{
          $("#count").css({color: '#333', fontWeight: 'normal'});
        }
      });
    })
    </script>
    <footer>
      <p>Copyright (C) 2007-2021 dia-electron Co., Ltd. All Rights Reserved.</p>
    </footer>
    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  </html>
