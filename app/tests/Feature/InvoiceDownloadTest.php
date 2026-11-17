<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\InvoiceController;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InvoiceDownloadTest extends TestCase
{
    #[DataProvider('downloads')]
    public function test_pdf_is_returned_without_sending_output(string $method, string $filename): void
    {
        $order = Mockery::mock(Order::class)->makePartial();
        $order->id = 6101;
        $order->price_net = 0;
        $order->vat = 0;
        $order->price_gross = 0;
        $order->setRelation('customer', new Customer(['name' => 'Test customer']));
        $order->setRelation('order_items', collect());
        $order->shouldReceive('load')->once()->with('customer', 'order_items.product')->andReturnSelf();
        $order->shouldReceive('calcPriceNet')->andReturn(0);

        $this->expectOutputString('');

        $response = (new InvoiceController)->{$method}(Request::create('/'), $order);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertSame('attachment; filename="'.$filename.'"', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public static function downloads(): array
    {
        return [
            'quote' => ['downloadQuote', 'Preventivo_006101.pdf'],
            'delivery note' => ['downloadDeliveryNote', 'Bolla di consegna_006101.pdf'],
        ];
    }
}
