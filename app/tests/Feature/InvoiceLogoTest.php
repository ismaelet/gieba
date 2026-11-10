<?php

namespace Tests\Feature;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InvoiceLogoTest extends TestCase
{
    public function test_invoice_logo_can_be_embedded_in_a_pdf(): void
    {
        $template = File::get(resource_path('html/invoice.html'));
        $logo = File::get(resource_path('images/logo/invoice-logo.png'));

        $this->assertStringContainsString('data:image/png;base64,{{ logo }}', $template);

        $dompdf = new Dompdf;
        $dompdf->loadHtml(str_replace('{{ logo }}', base64_encode($logo), $template));
        $dompdf->render();

        $pdf = $dompdf->output();
        $this->assertStringStartsWith('%PDF-', $pdf);
        $this->assertStringContainsString('/Subtype /Image', $pdf);
    }
}
