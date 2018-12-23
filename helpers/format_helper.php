<?php
/*
 * Format the date
 */
 function formatDateTime($date) {
   setlocale (LC_TIME, "no_EN");

   return strftime("%d. %B %Y kl %R",strtotime($date));
 }

function formatDate($date) {
    setlocale (LC_TIME, "no_EN");

    return strftime("%d. %B %Y",strtotime($date));
}

 /*
  * Shorten text
  */
  function shortenText($text, $chars = 490) {
    $text = substr($text, 0, $chars);
    $text = substr($text, 0, strrpos($text,' '));
    $text = $text."...";
    return $text;
  }

