<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Orchid\Support\Color;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class InvoiceCreateScreen extends Screen
{
    public $products;
    public $cart;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            $this->products=Product::all()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'InvoiceCreateScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::accordion([
                "General Information" => [
                    Layout::rows([
                        Input::make('invoice.seller_name')
                            ->title('Seller Name'),
                        Input::make('invoice.customer_name')
                            ->title('Costumer Name'),
                        Input::make('invoice.driver_name')
                            ->title('Driver Name'),
                        Input::make('invoice.description')
                            ->title('Description'),
                        Input::make('invoice.address')
                            ->title('Address'),
                        Input::make('invoice.recipient')
                            ->title('Recipient')
                    ])
                ],
                "Payment" => [
                    Layout::rows([
                        RadioButtons::make('invoice.status')
                            ->title('Status')
                            ->options([
                                "in_progress" => "in_progress",
                                "delivered" => "delivered",
                                "canceled" => "canceled",
                                "confirmation" => "confirmation",
                            ]),
                        Input::make('invoice.pay_deadline')
                            ->title('Pay Deadline')
                            ->type('date')
                    ])
                ]
            ]),
            Layout::rows([Select::make('product.id')
                ->fromQuery(Product::query(), 'display_name')
                ->searchColumns('product_name',)
                ->title('Product'),
                Button::make('submit')
                    ->type(Color::BASIC())
                    ->method('addToCart')
            ]),

        ];
    }

    public function addToCart(Request $request)
    {
        $this->cart+=$request->input('product.id');

    }


    public function store(Request $request)
    {
        dd($request->all());

    }
}
