<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Show the application dashboard
     */
    public function index()
    {
        // send mail
        // if (isset($_POST['submit']) && !$sent) {

        //     $name = trim($_POST['name']);
        //     $mail = trim($_POST['mail']);
        //     $msg = trim($_POST['text']);

        //     $serv = explode('@', $mail)[1];
        //     $serv = explode('.', $serv)[0];

        //     if (!in_array($serv, ['tiscali', 'gmail', 'libero', 'yahoo', 'hotmail', 'icloud', 'outlook'])) $error = 'mail_bad';
        //     else if (preg_match("/(sexy|bitcoin|wellness|dollars|https|free|business)/i", $msg)) $error = 'word_bad';
        //     else {
        //         $to      = sysData('cont_mail');
        //         $subject = 'Richiesta informazioni';

        //         $headers =
        //             'From: GI&BA Site <no-reply@shippingdog.com>' . "\r\n" .
        //             'Reply-To: ' . $mail . "\r\n" .
        //             'X-Mailer: PHP/' . phpversion();
        //         $headers .= 'MIME-Version: 1.0' . "\r\n";
        //         $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        //         $message = 'Messaggio da ' . $name . ' (' . $mail . '):<br/><br/>' . $msg;

        //         mail($to, $subject, $message, $headers);

        //         $sent = true;
        //     }
        // }

        return view('home');
    }
}
