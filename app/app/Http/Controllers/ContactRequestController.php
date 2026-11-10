<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ContactRequestController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'text' => 'required|string|max:1000',
        ]);

        $name = trim($request->name);
        $clientEmail = trim($request->email);
        $emailDomain = substr(strrchr($clientEmail, '@'), 1);
        $mailText = trim($request->text);

        $allowedEmailDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'libero.it', 'hotmail.it', 'icloud.com', 'protonmail.com', 'tiscali.it', 'virgilio.it', 'alice.it', 'fastwebnet.it', 'email.it', 'gmx.it', 'inwind.it', 'tin.it', 'tim.it', 'yahoo.it'];
        $bannedWords = ['CEO'];

        foreach ($bannedWords as $word) {
            if (str_contains($mailText, $word)) {
                return redirect()->back()->withInput()->withErrors([
                    'spam' => '<span class="font-bold">Errore</span>: il messaggio non ha superato i nostri filtri anti-spam. Puoi comunque a scriverci direttamente a
							<a href="'.config('custom.contacts.email.url').'" class="font-bold">'.config('custom.contacts.email.label').'</a>, invece di usare questo formulario.',
                ]);
            }
        }

        if (! in_array($emailDomain, $allowedEmailDomains)) {
            return redirect()->back()->withInput()->withErrors([
                'spam' => '<span class="font-bold">Errore</span>: il messaggio non ha superato i nostri filtri anti-spam. Puoi comunque a scriverci direttamente a
                                <a href="'.config('custom.contacts.email.url').'" class="font-bold">'.config('custom.contacts.email.label').'</a>, invece di usare questo formulario.',
            ])->withFragment('contacts');
        }

        try {
            // Send message
            $content = '<h1 style="text-align: center; padding-bottom: 16px; font-weight: 600;">Richiesta informazioni</h1>
		<p>Hai ricevuto una richiesta di informazioni da parte di <strong>'.$name.'</strong> (<a href="mailto:'.$clientEmail.'">'.$clientEmail.'</a>) con la seguente domanda:<br><br>"'.$mailText.'"</p>';

            Mail::to(config('custom.notification_emails'))
                ->send(new ContactMail(
                    email: $clientEmail,
                    name: $name,
                    text: $content
                ));

            return redirect()->back()->with(
                'success',
                'Messaggio inviato, ti risponderemo al più presto'
            )
                ->withFragment('contacts');
        } catch (TransportExceptionInterface $e) {
            return redirect()->back()->withInput()->withErrors([
                'spam' => '<span class="font-bold">Errore</span>: il messaggio non ha superato i nostri filtri anti-spam. Puoi comunque a scriverci direttamente a
							<a href="'.config('custom.contacts.email.url').'" class="font-bold">'.config('custom.contacts.email.label').'</a>, invece di usare questo formulario.',
            ])
                ->withFragment('contacts');
        }
    }
}
