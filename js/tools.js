/*
 * Shorten text
 */

 function shortenText(text, maxLength) {
   if (text.length > maxLength) {
     text = text.substr(0, maxLength);
     text = text.substr(0, text.lastIndexOf(' ', text));
     text += " &hellip;";
   }
   return text;
 }
