<?php
session_start();
include "base/config.php";
include "base/baseObject.php";
function checkLogin(){
  if(!isset($_SESSION['username'])){
    header('Location: index.php');
  }
}
$pages = $_GET['page'];
switch ($pages) {
  case 'refMember':{
    checkLogin();
    include "pages/refMember/refMember.php";
    break;
  }
  case 'refTradePoint':{
    checkLogin();
    include "pages/refTradePoint/refTradePoint.php";
    break;
  }
  case 'refPayment':{
    checkLogin();
    include "pages/refPayment/refPayment.php";
    break;
  }
  case 'refNews':{
    checkLogin();
    include "pages/refNews/refNews.php";
    break;
  }
  case 'logAbsen':{
    checkLogin();
    include "pages/logAbsen/logAbsen.php";
    break;
  }
  case 'logAdRequest':{
    checkLogin();
    include "pages/logAdRequest/logAdRequest.php";
    break;
  }
  case 'logAdShow':{
    checkLogin();
    include "pages/logAdShow/logAdShow.php";
    break;
  }
  case 'settingAd':{
    checkLogin();
    include "pages/settingAd/settingAd.php";
    break;
  }


  case 'logout':{
    $_SESSION['username'] = '';
    unset($_SESSION['username']);
    session_destroy();
    checkLogin();
    break;
  }
  default:{
    checkLogin();
    include "pages/dashboard.php";
    break;
  break;
  }
}



 ?>
