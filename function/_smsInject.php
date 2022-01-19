<?php

//$dbSMS['site'] = "localhost";
//$dbSMS['db'] = "sms";

require_once('./config.php' );
require_once('secureParam.php');
//require_once('../function/secureParam.php');

function sendSms($msg, $dest, $sender="",$db=NULL) {
global $dbLink;
//  if (!(isset($db))) {
//    global $dbSMS;
//    $db = mysql_connect($dbSMS['site'],$dbSMS['username'],$dbSMS['password']);
//    mysql_select_db($dbSMS['db'],$db);
//  }

  if (strlen($msg)<=160) {
    if (is_array($dest)) {
      foreach ($dest as $_dest) {
        if ($_dest=="") continue;
        send($msg,$_dest,$sender);
      }
    } else send($msg,$dest,$sender);
  } else {
    if (is_array($dest)) {
      foreach ($dest as $_dest) {
        if ($_dest=="") continue;
        sendLong($msg,$_dest,$sender);
      }
    } else sendLong($msg,$dest,$sender);
  }

  if (!(isset($db))) mysql_close($db);
}

function send($msg,$dest,$sender) {
    global $dbLink;
  $q = "show table status where name='outbox'";
  $res = mysql_query($q);
  $res = mysql_fetch_assoc($res);
  
  $msg = str_replace("'","''",$msg);
  
  $q = "insert into outbox (DestinationNumber, TextDecoded) values ('".$dest."','".$msg."');";
  mysql_query($q,$dbLink);
  $q = "insert into creator (smsId, creator) values ('".$res['Auto_increment']."','".$sender."');";
  mysql_query($q,$dbLink);
  
}

function padHex($str) {
    global $dbLink;
  return str_pad(dechex($str),2,'0',STR_PAD_LEFT);
}

function sendLong($msg,$dest,$sender) {
    global $dbLink;
  $udh = "050003".padHex(mt_rand(1,255));
  $msg = str_split($msg,153);
  $udh .= padHex(count($msg));
  
  $q = "show table status where name='outbox'";
  $res = mysql_query($q, $dbLink);
  $res = mysql_fetch_assoc($res);
  $id = $res['Auto_increment'];
  
  $x=1;
  foreach ($msg as $_msg) {
    $_udh = $udh.padHex($x);
    $_msg = str_replace("'","''",$_msg);

    if ($x==1) {
      $q = "insert into outbox (DestinationNumber, TextDecoded, UDH, MultiPart) values ('".$dest."','".$_msg."','".$_udh."','true');";
      mysql_query($q, $dbLink);
      $q = "insert into creator (smsId, creator) values ('".$id."','".$sender."');";
      mysql_query($q, $dbLink);
    } else {
      $q = "insert into outbox_multipart (TextDecoded, UDH, ID, SequencePosition) values ('".$_msg."','".$_udh."','".$id."','".$x."');";
      mysql_query($q, $dbLink);
    }
    $x++;
  }

}


/*
-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2011 at 05:49 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `creator`
--

CREATE TABLE IF NOT EXISTS `creator` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `smsId` int(11) NOT NULL,
  `creator` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;
*/



?>
