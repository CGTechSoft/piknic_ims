<?php

//include './Log.php';
//include './Constants.php';

/**
 * Description of mail
 *
 * @author Sakkeer Hussain
 */
class mail {
    public $to = 'cgtsoft@gmail.com, sakkeerhussainp@gmail.com, piknikdates@gmail.com';
    public $subject = 'Error Reporting from Pik Nik IMS';
    public $header = 'From: webmaster@piknikindia.in';
    public $tag = "MAIL CONTROLLER";
    public function send_error_mail($query, $error){
        $message = '
            <html>
            <head>
              <title style="font-size:28px;">'.$this->subject.'</title>
            </head>
            <body style="background-color:#FFD00F; color:#fff;">
              <p>'.$this->subject.'</p>
              <table>
                <tr>
                  <td style="font-size:25px;">Query</td>
                </tr>
                <tr>
                  <td>'.$query.'</td>
                </tr>
                <tr><td><hr/></td></tr>
                <tr>
                  <td style="font-size:25px;">Error</td>
                </tr>
                <tr>
                  <td>'.$error.'</td>
                </tr>
              </table>
            </body>
            </html>
            ';
        $headers  = $this->header . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $result = mail($this->to, $this->subject, $message, $headers);
        $description = "mail sent, query : $query, error : $error, result : $result ";
        Log::i($this->tag, $description);
    }
}

//$mail = new mail();
//$mail->send_error_mail("fhdh", "fdhdh");
